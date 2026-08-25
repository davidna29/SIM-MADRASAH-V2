<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\ClassGroup;
use App\Models\Offense;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestasiPelanggaranModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $wali;

    protected User $guru;

    protected User $bk;

    protected User $kepala;

    protected StudentEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kesiswaan']);
        $this->wali = User::factory()->create(['role' => 'wali_kelas']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->bk = User::factory()->create(['role' => 'guru_bk']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $person = Person::create(['nik' => '3525100101', 'name' => 'Aisyah', 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '251001', 'name' => 'Aisyah', 'gender' => 'P']);

        $this->enrollment = StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function achievementPayload(): array
    {
        return [
            'student_id' => $this->enrollment->student_id,
            'jenis' => 'akademik',
            'nama_kegiatan' => 'Lomba Matematika',
            'tingkat' => 'nasional',
            'penyelenggara' => 'Kemendikbud',
            'tanggal' => '2026-07-01',
            'peringkat' => 'Juara 2',
            'pembimbing' => 'Bapak Umar',
            'status_publikasi' => 'publik',
        ];
    }

    protected function offensePayload(): array
    {
        return [
            'student_id' => $this->enrollment->student_id,
            'kategori' => 'Berkelahi',
            'tingkat' => 'berat',
            'poin' => 15,
            'tanggal_kejadian' => '2026-07-05',
            'kronologi' => 'Terlibat perkelahian di lingkungan madrasah.',
            'pelapor' => 'Guru Piket',
            'status_penyelesaian' => 'proses',
        ];
    }

    public function test_writer_can_record_achievement(): void
    {
        $this->actingAs($this->wali)->post(route('prestasi.store'), $this->achievementPayload())->assertSessionHasNoErrors();

        $this->assertDatabaseHas('achievements', [
            'student_id' => $this->enrollment->student_id,
            'nama_kegiatan' => 'Lomba Matematika',
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function test_wakamad_can_verify_and_toggle_publikasi(): void
    {
        $this->actingAs($this->guru)->post(route('prestasi.store'), $this->achievementPayload());
        $achievement = Achievement::first();

        $this->actingAs($this->wakamad)->post(route('prestasi.verifikasi', $achievement), ['status_verifikasi' => 'terverifikasi']);
        $this->actingAs($this->wakamad)->post(route('prestasi.publikasi', $achievement), ['status_publikasi' => 'internal']);

        $achievement->refresh();
        $this->assertSame('terverifikasi', $achievement->status_verifikasi);
        $this->assertSame('internal', $achievement->status_publikasi);
    }

    public function test_kepala_can_view_but_not_create_achievement(): void
    {
        $this->actingAs($this->kepala)->get(route('prestasi.index'))->assertOk();
        $this->actingAs($this->kepala)->post(route('prestasi.store'), $this->achievementPayload())->assertForbidden();
        $this->actingAs($this->bk)->post(route('prestasi.store'), $this->achievementPayload())->assertForbidden();
    }

    public function test_offense_crud_and_role_access(): void
    {
        $this->actingAs($this->bk)->post(route('pelanggaran.store'), $this->offensePayload())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('offenses', ['kategori' => 'Berkelahi', 'status_penyelesaian' => 'proses']);

        $this->actingAs($this->kepala)->get(route('pelanggaran.index'))->assertOk();
        $this->actingAs($this->kepala)->post(route('pelanggaran.store'), $this->offensePayload())->assertForbidden();
    }

    public function test_delete_only_for_admin_roles(): void
    {
        $this->actingAs($this->wakamad)->post(route('pelanggaran.store'), $this->offensePayload());
        $offense = Offense::first();

        $this->actingAs($this->bk)->delete(route('pelanggaran.destroy', $offense))->assertForbidden();
        $this->actingAs($this->wakamad)->delete(route('pelanggaran.destroy', $offense))->assertRedirect();
        $this->assertDatabaseCount('offenses', 0);
    }

    public function test_offense_poin_validation(): void
    {
        $payload = $this->offensePayload();
        $payload['poin'] = 150;

        $this->actingAs($this->wakamad)->post(route('pelanggaran.store'), $payload)->assertSessionHasErrors('poin');
    }
}
