<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapRaporModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $kepala;

    protected User $guru;

    protected ClassGroup $kelas;

    protected Subject $mat;

    protected TeacherAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kurikulum']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Umar']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $this->mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'sort_order' => 1]);

        $this->assignment = TeacherAssignment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'subject_id' => $this->mat->id,
            'user_id' => $this->guru->id,
        ]);
    }

    protected function makeStudent(string $nis, string $name): StudentEnrollment
    {
        $student = Student::create(['nis' => $nis, 'name' => $name, 'gender' => 'L']);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function score(StudentEnrollment $enrollment, int $value): void
    {
        Score::updateOrCreate(
            [
                'student_enrollment_id' => $enrollment->id,
                'subject_id' => $this->mat->id,
                'academic_year_id' => AcademicYear::active()->id,
                'semester' => AcademicYear::active()->semester,
            ],
            ['score' => $value]
        );
    }

    protected function publish(StudentEnrollment $enrollment): void
    {
        $this->actingAs($this->guru)->post(route('guru.nilai.terbitkan', $this->assignment));
    }

    // ==== Otorisasi ====

    public function test_admin_wakamad_kepala_can_open_rekap(): void
    {
        $this->actingAs($this->admin)->get(route('akademik.rapor.index'))->assertOk();
        $this->actingAs($this->wakamad)->get(route('akademik.rapor.index'))->assertOk();
        $this->actingAs($this->kepala)->get(route('akademik.rapor.index'))->assertOk();
    }

    public function test_guru_cannot_open_rekap(): void
    {
        $this->actingAs($this->guru)->get(route('akademik.rapor.index'))->assertForbidden();
    }

    // ==== Agregasi ====

    public function test_rekap_shows_correct_totals(): void
    {
        $enal = $this->makeStudent('251001', 'Aisyah');
        $enb = $this->makeStudent('251002', 'Bilal');
        $enc = $this->makeStudent('251003', 'Citra');

        // Aisyah punya nilai & rapor terbit; Bilal punya nilai saja; Citra kosong.
        $this->score($enal, 85);
        $this->publish($enal);
        $this->score($enb, 78);

        $response = $this->actingAs($this->admin)->get(route('akademik.rapor.index'));

        $response->assertOk();
        $response->assertSee('I-A');
        $response->assertSee('Detail');
    }

    public function test_rekap_can_filter_by_class(): void
    {
        $this->makeStudent('251004', 'Dimas');

        $response = $this->actingAs($this->admin)->get(route('akademik.rapor.index', ['class_group_id' => $this->kelas->id]));

        $response->assertOk();
        $response->assertSee('I-A');
    }

    // ==== Detail kelas & siswa ====

    public function test_kelas_detail_shows_students_and_subjects(): void
    {
        $enrollment = $this->makeStudent('251005', 'Eka');
        $this->score($enrollment, 85);

        $response = $this->actingAs($this->admin)->get(route('akademik.rapor.kelas', $this->kelas));

        $response->assertOk();
        $response->assertSee('Eka');
        $response->assertSee('MAT');
    }

    public function test_siswa_detail_shows_scores(): void
    {
        $enrollment = $this->makeStudent('251006', 'Fajar');
        $this->score($enrollment, 88);

        $response = $this->actingAs($this->wakamad)->get(route('akademik.rapor.siswa', $enrollment));

        $response->assertOk();
        $response->assertSee('Fajar');
        $response->assertSee('Matematika');
        $response->assertSee('88');
    }

    public function test_guru_cannot_open_kelas_or_siswa_detail(): void
    {
        $enrollment = $this->makeStudent('251007', 'Gita');

        $this->actingAs($this->guru)->get(route('akademik.rapor.kelas', $this->kelas))->assertForbidden();
        $this->actingAs($this->guru)->get(route('akademik.rapor.siswa', $enrollment))->assertForbidden();
    }
}
