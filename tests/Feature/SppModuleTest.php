<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\TuitionOverride;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;

    protected User $admin;

    protected User $kepala;

    protected User $guru;

    protected User $waliKelas;

    protected ClassGroup $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::factory()->create(['role' => 'bendahara', 'name' => 'Ibu Fitri']);
        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->waliKelas = User::factory()->create(['role' => 'wali_kelas']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
    }

    protected function makeStudent(string $nis, string $name): StudentEnrollment
    {
        $person = Person::create(['nik' => '35'.str_pad($nis, 12, '0'), 'name' => $name, 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => $name, 'gender' => 'P']);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function payPayload(StudentEnrollment $enrollment, array $overrides = []): array
    {
        return array_merge([
            'student_enrollment_id' => $enrollment->id,
            'bulan' => 7,
            'nominal' => 100000,
            'tanggal_bayar' => now()->toDateString(),
            'metode' => 'tunai',
            'catatan' => null,
        ], $overrides);
    }

    public function test_bendahara_can_set_default_nominal_and_override(): void
    {
        $tahun = AcademicYear::active();
        $enrollment = $this->makeStudent('251001', 'Aisyah');

        $this->actingAs($this->bendahara)->post(route('spp.settings.store'), [
            'nominal' => [$tahun->id => 150000],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tuition_settings', ['academic_year_id' => $tahun->id, 'nominal' => 150000]);

        $this->actingAs($this->bendahara)->post(route('spp.overrides.store'), [
            'student_enrollment_id' => $enrollment->id,
            'nominal' => 75000,
            'keterangan' => 'Yatim',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tuition_overrides', ['student_enrollment_id' => $enrollment->id, 'nominal' => 75000]);
    }

    public function test_bendahara_can_record_payment_and_status_becomes_lunas(): void
    {
        $enrollment = $this->makeStudent('251002', 'Bilal');

        $response = $this->actingAs($this->bendahara)->post(route('spp.pay'), $this->payPayload($enrollment));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tuition_payments', [
            'student_enrollment_id' => $enrollment->id,
            'bulan' => 7,
            'nominal' => 100000,
            'status' => 'lunas',
        ]);
    }

    public function test_reinput_same_month_updates_instead_of_duplicating(): void
    {
        $enrollment = $this->makeStudent('251003', 'Citra');

        $this->actingAs($this->bendahara)->post(route('spp.pay'), $this->payPayload($enrollment, ['nominal' => 100000]));
        $this->actingAs($this->bendahara)->post(route('spp.pay'), $this->payPayload($enrollment, ['nominal' => 120000]));

        $this->assertSame(1, TuitionPayment::where('student_enrollment_id', $enrollment->id)->where('bulan', 7)->count());
        $this->assertDatabaseHas('tuition_payments', ['student_enrollment_id' => $enrollment->id, 'bulan' => 7, 'nominal' => 120000]);
    }

    public function test_kepala_madrasah_can_view_index_but_not_store(): void
    {
        $enrollment = $this->makeStudent('251004', 'Dewi');

        $this->actingAs($this->kepala)->get(route('spp.index', ['class_group_id' => $this->kelas->id]))->assertOk();
        $this->actingAs($this->kepala)->post(route('spp.pay'), $this->payPayload($enrollment))->assertForbidden();
        $this->actingAs($this->kepala)->get(route('spp.settings'))->assertForbidden();
        $this->actingAs($this->kepala)->get(route('spp.overrides'))->assertForbidden();
    }

    public function test_guru_wali_kelas_and_ortu_cannot_access_tuition_routes(): void
    {
        $enrollment = $this->makeStudent('251005', 'Eka');
        $ortu = User::factory()->create(['role' => 'orang_tua']);

        foreach ([$this->guru, $this->waliKelas, $ortu] as $user) {
            $this->actingAs($user)->get(route('spp.index'))->assertForbidden();
            $this->actingAs($user)->post(route('spp.pay'), $this->payPayload($enrollment))->assertForbidden();
        }
    }

    public function test_ortu_can_only_see_own_child_spp(): void
    {
        $anakA = $this->makeStudent('251006', 'Anak A');
        $anakB = $this->makeStudent('251007', 'Anak B');

        $this->actingAs($this->bendahara)->post(route('spp.pay'), $this->payPayload($anakA));

        $ortu = User::factory()->create(['role' => 'orang_tua', 'name' => 'Ibu A']);
        $guardian = Guardian::create(['user_id' => $ortu->id, 'name' => 'Ibu A']);
        $guardian->students()->attach($anakA->student_id);

        $this->actingAs($ortu)->get(route('ortu.spp.index'))->assertOk()->assertSee('Anak A');
        $this->actingAs($ortu)->get(route('ortu.spp.show', $anakA->student_id))->assertOk()->assertSee('Lunas');
        $this->actingAs($ortu)->get(route('ortu.spp.show', $anakB->student_id))->assertForbidden();
    }

    public function test_zero_nominal_is_allowed(): void
    {
        $enrollment = $this->makeStudent('251009', 'Gita');

        $this->actingAs($this->bendahara)->post(route('spp.pay'), $this->payPayload($enrollment, ['nominal' => 0]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tuition_payments', ['student_enrollment_id' => $enrollment->id, 'nominal' => 0]);

        $this->actingAs($this->bendahara)->post(route('spp.overrides.store'), [
            'student_enrollment_id' => $enrollment->id,
            'nominal' => 0,
            'keterangan' => 'Bebas SPP',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tuition_overrides', ['student_enrollment_id' => $enrollment->id, 'nominal' => 0]);
    }

    public function test_override_nominal_is_used_when_exists(): void
    {
        $enrollment = $this->makeStudent('251008', 'Fajar');

        TuitionSetting::create(['academic_year_id' => AcademicYear::active()->id, 'nominal' => 100000]);
        TuitionOverride::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => AcademicYear::active()->id,
            'nominal' => 75000,
            'keterangan' => 'Keringanan',
        ]);

        $response = $this->actingAs($this->bendahara)->get(route('spp.index', ['class_group_id' => $this->kelas->id]));

        $response->assertOk();
        $response->assertSee('Rp 75.000');
    }
}
