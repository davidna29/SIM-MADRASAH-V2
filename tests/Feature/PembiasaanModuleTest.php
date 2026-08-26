<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\PembiasaanMateri;
use App\Models\PembiasaanNilai;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\PembiasaanService;
use Database\Seeders\PpiMateriSeeder;
use Database\Seeders\TahfidzMateriSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembiasaanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $wali;

    protected User $guru;

    protected User $bk;

    protected User $kepala;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $year = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $person = Person::create(['nik' => '3525100101', 'name' => 'Budi', 'gender' => 'L', 'religion' => 'Islam']);
        $this->student = Student::create(['person_id' => $person->id, 'nis' => '260001', 'name' => 'Budi', 'gender' => 'L']);
        StudentEnrollment::create([
            'academic_year_id' => $year->id,
            'class_group_id' => $kelas->id,
            'student_id' => $this->student->id,
            'status' => 'aktif',
        ]);

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kesiswaan']);
        $this->wali = User::factory()->create(['role' => 'wali_kelas']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->bk = User::factory()->create(['role' => 'guru_bk']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);

        $this->seed([PpiMateriSeeder::class, TahfidzMateriSeeder::class]);
    }

    public function test_ppi_index_renders_for_wali(): void
    {
        $this->actingAs($this->wali)->get(route('ppi.index'))->assertOk()->assertSee('PPI');
    }

    public function test_tahfidz_index_renders_for_wali(): void
    {
        $this->actingAs($this->wali)->get(route('tahfidz.index'))->assertOk()->assertSee('Tahfidz');
    }

    public function test_guru_bk_cannot_view_ppi(): void
    {
        $this->actingAs($this->bk)->get(route('ppi.index'))->assertForbidden();
    }

    public function test_wali_can_store_nilai_on_active_cell(): void
    {
        $materi = PembiasaanMateri::where('modul', 'ppi')->where('no_urut', 1)->first();

        $this->actingAs($this->wali)
            ->post(route('ppi.store', $this->student), ['nilai' => [$materi->id => 85]])
            ->assertRedirect(route('ppi.input', $this->student));

        $this->assertDatabaseHas('pembiasaan_nilai', [
            'siswa_id' => $this->student->id,
            'materi_id' => $materi->id,
            'kelas' => 1,
            'semester' => 1,
            'nilai' => 85,
        ]);
    }

    public function test_store_ignores_locked_inactive_cell(): void
    {
        // Materi PPI No.10 mulai berlaku Kelas II → tidak aktif di (1,1)
        $materi = PembiasaanMateri::where('modul', 'ppi')->where('no_urut', 10)->first();

        $this->actingAs($this->wali)
            ->post(route('ppi.store', $this->student), ['nilai' => [$materi->id => 90]])
            ->assertRedirect(route('ppi.input', $this->student));

        $this->assertDatabaseMissing('pembiasaan_nilai', [
            'siswa_id' => $this->student->id,
            'materi_id' => $materi->id,
        ]);
    }

    public function test_kepala_cannot_store_nilai(): void
    {
        $materi = PembiasaanMateri::where('modul', 'ppi')->where('no_urut', 1)->first();

        $this->actingAs($this->kepala)
            ->post(route('ppi.store', $this->student), ['nilai' => [$materi->id => 80]])
            ->assertForbidden();
    }

    public function test_footer_perhitungan_jumlah_rata_kategori(): void
    {
        $service = app(PembiasaanService::class);

        // Buat nilai untuk SEMUA materi PPI aktif di (1,1) agar rata-rata benar
        $active = PembiasaanMateri::forModul('ppi')
            ->whereHas('periodes', fn ($q) => $q->where('kelas', 1)->where('semester', 1)->where('aktif', true))
            ->get();

        foreach ($active as $m) {
            PembiasaanNilai::create([
                'siswa_id' => $this->student->id,
                'materi_id' => $m->id,
                'kelas' => 1,
                'semester' => 1,
                'tahun_pelajaran' => '2026/2027',
                'nilai' => 80,
            ]);
        }

        $f = $service->allFooters($this->student, 'ppi')['1-1'];

        $this->assertEquals($active->count() * 80, $f['jumlah']);
        $this->assertEquals(80, $f['rata_rata']);
        $this->assertEquals('A', $f['kategori']);
    }

    public function test_kategori_mapping(): void
    {
        $this->assertEquals('A+', PembiasaanService::kategori(95));
        $this->assertEquals('A', PembiasaanService::kategori(85));
        $this->assertEquals('B', PembiasaanService::kategori(75));
        $this->assertEquals('D', PembiasaanService::kategori(55));
        $this->assertEquals('–', PembiasaanService::kategori(40));
    }

    public function test_admin_can_open_konfigurasi_but_wali_cannot(): void
    {
        $this->actingAs($this->admin)->get(route('ppi.konfigurasi'))->assertOk();
        $this->actingAs($this->wali)->get(route('ppi.konfigurasi'))->assertForbidden();
    }

    public function test_konfigurasi_toggle_updates_periode(): void
    {
        $materi = PembiasaanMateri::where('modul', 'ppi')->where('no_urut', 10)->first();

        $this->actingAs($this->admin)
            ->post(route('ppi.konfigurasi.update'), ['periode' => ["{$materi->id}-1-1" => '1']])
            ->assertRedirect(route('ppi.konfigurasi'));

        $this->assertDatabaseHas('pembiasaan_materi_periode', [
            'materi_id' => $materi->id,
            'kelas' => 1,
            'semester' => 1,
            'aktif' => true,
        ]);

        // Reset-all-then-enable: materi No.1 (1,1) yang tadinya aktif jadi nonaktif
        $m1 = PembiasaanMateri::where('modul', 'ppi')->where('no_urut', 1)->first();
        $this->assertDatabaseHas('pembiasaan_materi_periode', [
            'materi_id' => $m1->id,
            'kelas' => 1,
            'semester' => 1,
            'aktif' => false,
        ]);
    }

    public function test_cetak_preview_renders(): void
    {
        $response = $this->actingAs($this->wali)->get(route('ppi.cetak', $this->student));

        $response->assertOk();
        $response->assertSee('PRAKTEK PENGAMALAN IBADAH');
        $response->assertSee('Download PDF');
        $response->assertSee('Export Excel');
    }

    public function test_cetak_pdf_downloads(): void
    {
        $response = $this->actingAs($this->wali)->get(route('ppi.cetak.pdf', $this->student));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_tahfidz_material_window_respected_on_store(): void
    {
        // Tahfidz No.14 aktif hanya (1,2),(6,1),(6,2) → tidak aktif di (1,1)
        $materi = PembiasaanMateri::where('modul', 'tahfidz')->where('no_urut', 14)->first();

        $this->actingAs($this->wali)
            ->post(route('tahfidz.store', $this->student), ['nilai' => [$materi->id => 88]])
            ->assertRedirect(route('tahfidz.input', $this->student));

        $this->assertDatabaseMissing('pembiasaan_nilai', [
            'siswa_id' => $this->student->id,
            'materi_id' => $materi->id,
        ]);
    }
}
