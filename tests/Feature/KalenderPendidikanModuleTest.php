<?php

namespace Tests\Feature;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\Akademik\AcademicCalendarService;
use Database\Seeders\MisnuCalendarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KalenderPendidikanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $tataUsaha;

    protected User $kepala;

    protected User $guru;

    protected User $orangTua;

    protected AcademicYear $tahun;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['username' => 'admin', 'role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kurikulum']);
        $this->tataUsaha = User::factory()->create(['role' => 'tata_usaha']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->orangTua = User::factory()->create(['role' => 'orang_tua']);

        $this->tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        AcademicCalendarPeriod::create([
            'academic_year_id' => $this->tahun->id,
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'effective_weeks' => 19,
            'effective_days' => 115,
        ]);

        AcademicCalendarPeriod::create([
            'academic_year_id' => $this->tahun->id,
            'semester' => 'genap',
            'start_date' => '2027-01-04',
            'end_date' => '2027-06-19',
            'effective_weeks' => 17,
            'effective_days' => 100,
        ]);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Rapat Dewan Guru',
            'category' => 'kegiatan',
            'start_date' => '2026-11-25',
            'end_date' => null,
            'is_libur' => false,
            'is_efektif' => true,
            'description' => 'Persiapan asesmen.',
        ], $overrides);
    }

    public function test_index_can_be_opened_by_viewer_roles(): void
    {
        foreach ([$this->admin, $this->wakamad, $this->kepala, $this->guru, $this->tataUsaha] as $user) {
            $this->actingAs($user)
                ->get(route('kalender.index', ['bulan' => 11, 'tahun' => 2026]))
                ->assertOk()
                ->assertSee('Kalender Pendidikan')
                ->assertSee('Pekan Efektif');
        }
    }

    public function test_other_roles_are_forbidden(): void
    {
        $this->actingAs($this->orangTua)->get(route('kalender.index'))->assertForbidden();
    }

    public function test_manager_can_create_event(): void
    {
        $this->actingAs($this->wakamad)
            ->post(route('kalender.store'), $this->payload(['is_libur' => true]))
            ->assertRedirect();

        $this->assertDatabaseHas('academic_calendar_events', [
            'academic_year_id' => $this->tahun->id,
            'title' => 'Rapat Dewan Guru',
            'is_libur' => true,
            'is_efektif' => true,
        ]);
    }

    public function test_guru_cannot_create_event(): void
    {
        $this->actingAs($this->guru)
            ->post(route('kalender.store'), $this->payload())
            ->assertForbidden();
    }

    public function test_end_date_before_start_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('kalender.store'), $this->payload([
                'start_date' => '2026-11-25',
                'end_date' => '2026-11-20',
            ]))
            ->assertSessionHasErrors('end_date');
    }

    public function test_event_can_be_updated_and_deleted(): void
    {
        $event = AcademicCalendarEvent::create([
            'academic_year_id' => $this->tahun->id,
            'title' => 'Kegiatan Lama',
            'category' => 'kegiatan',
            'start_date' => '2026-11-10',
        ]);

        $this->actingAs($this->tataUsaha)
            ->put(route('kalender.update', $event), $this->payload(['title' => 'Kegiatan Baru']))
            ->assertRedirect();

        $this->assertDatabaseHas('academic_calendar_events', ['id' => $event->id, 'title' => 'Kegiatan Baru']);

        $this->actingAs($this->tataUsaha)
            ->delete(route('kalender.destroy', $event))
            ->assertRedirect();

        $this->assertDatabaseMissing('academic_calendar_events', ['id' => $event->id]);
    }

    public function test_effective_days_are_calculated_from_holidays_and_non_effective_events(): void
    {
        $year = AcademicYear::create(['name' => '2027/2028', 'semester' => 'genap', 'is_active' => false]);
        AcademicCalendarPeriod::create([
            'academic_year_id' => $year->id,
            'semester' => 'genap',
            'start_date' => '2027-01-01',
            'end_date' => '2027-01-31',
        ]);

        AcademicCalendarEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Libur Awal Tahun',
            'category' => 'libur',
            'start_date' => '2027-01-01',
            'end_date' => '2027-01-02',
            'is_libur' => true,
            'is_efektif' => false,
        ]);

        AcademicCalendarEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Asesmen',
            'category' => 'asesmen',
            'start_date' => '2027-01-04',
            'end_date' => '2027-01-05',
            'is_libur' => false,
            'is_efektif' => false,
        ]);

        $summary = (new AcademicCalendarService)->monthSummary($year, 2027, 1, 'genap');

        // Januari 2027: 31 hari − 5 Minggu = 26 hari sekolah.
        // Libur 2 hari + asesmen non-efektif 2 hari = 22 hari efektif.
        $this->assertSame(26, $summary['hari_tersedia']);
        $this->assertSame(22, $summary['hari_efektif']);
        $this->assertSame(2, $summary['hari_libur']);
    }

    public function test_snapshot_values_are_exposed_in_summary(): void
    {
        $summary = (new AcademicCalendarService)->semesterSummary($this->tahun, 'ganjil');

        $this->assertSame(19, $summary['snapshot_pekan']);
        $this->assertSame(115, $summary['snapshot_hari']);
    }

    public function test_period_can_be_updated(): void
    {
        $this->actingAs($this->admin)
            ->post(route('kalender.periode'), [
                'semester' => 'ganjil',
                'start_date' => '2026-07-13',
                'end_date' => '2026-12-18',
                'effective_weeks' => 20,
                'effective_days' => 116,
                'note' => 'Revisi kalender.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('academic_calendar_periods', [
            'academic_year_id' => $this->tahun->id,
            'semester' => 'ganjil',
            'effective_weeks' => 20,
            'effective_days' => 116,
        ]);
    }

    public function test_print_and_public_pages_render(): void
    {
        $this->actingAs($this->kepala)->get(route('kalender.cetak'))->assertOk()->assertSee('KALENDER PENDIDIKAN');

        $this->get(route('publik.kalender'))->assertOk()->assertSee('Kalender Pendidikan');
    }

    public function test_seeder_fills_periods_and_documents(): void
    {
        $this->seed(MisnuCalendarSeeder::class);

        $this->assertSame(2, AcademicCalendarPeriod::where('academic_year_id', $this->tahun->id)->count());
        $this->assertTrue(AcademicCalendarPeriod::where('semester', 'ganjil')->where('effective_weeks', 19)->exists());
        $this->assertTrue(AcademicCalendarEvent::where('title', 'Libur Semester Gasal')->where('is_libur', true)->exists());
    }
}
