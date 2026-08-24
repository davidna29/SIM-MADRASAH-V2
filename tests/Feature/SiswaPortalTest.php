<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswa;

    protected ClassGroup $kelas;

    protected StudentEnrollment $enrollment;

    protected Student $anak;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $this->anak = $this->makeStudent('251001', 'Aisyah');
        $this->siswa = User::factory()->create(['role' => 'siswa', 'name' => 'Aisyah', 'student_id' => $this->anak->id]);
    }

    protected function makeStudent(string $nis, string $name): Student
    {
        $person = Person::create(['nik' => '35'.str_pad($nis, 12, '0'), 'name' => $name, 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => $name, 'gender' => 'P']);

        $this->enrollment = StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        return $student;
    }

    protected function seedReport(): void
    {
        $tahun = AcademicYear::active();
        $mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'sort_order' => 1]);

        $report = Report::create([
            'student_id' => $this->anak->id,
            'academic_year_id' => $tahun->id,
            'semester' => $tahun->semester,
            'version' => 1,
            'status' => 'terbit',
            'snapshot' => [
                'tahun' => $tahun->name,
                'semester' => $tahun->semester,
                'nis' => $this->anak->nis,
                'siswa' => $this->anak->name,
                'kelas' => 'I-A',
                'terbit_pada' => now()->toDateTimeString(),
            ],
        ]);

        $report->items()->create([
            'subject_code' => $mat->code,
            'subject_name' => $mat->name,
            'class_name' => 'I-A',
            'teacher_name' => 'Bapak Umar',
            'score' => 85,
            'sort_order' => 1,
        ]);
    }

    protected function seedAttendance(): void
    {
        $tahun = AcademicYear::active();

        Attendance::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $this->kelas->id,
            'student_enrollment_id' => $this->enrollment->id,
            'attendance_date' => '2026-07-03',
            'status' => 'hadir',
            'recorded_by' => $this->siswa->id,
        ]);
    }

    protected function seedSpp(): void
    {
        $tahun = AcademicYear::active();

        TuitionPayment::create([
            'student_enrollment_id' => $this->enrollment->id,
            'academic_year_id' => $tahun->id,
            'semester' => $tahun->semester,
            'bulan' => 7,
            'nominal' => 100000,
            'status' => 'lunas',
            'tanggal_bayar' => '2026-07-05',
            'metode' => 'tunai',
            'recorded_by' => $this->siswa->id,
        ]);
    }

    public function test_siswa_can_view_dashboard_with_own_data(): void
    {
        $this->seedReport();
        $this->seedAttendance();
        $this->seedSpp();

        $response = $this->actingAs($this->siswa)->get(route('siswa.dashboard'));

        $response->assertOk();
        $response->assertSee('Aisyah');
        $response->assertSee('Matematika');
        $response->assertSee('Juli');
        $response->assertSee('1 dari 6 bulan lunas');
    }

    public function test_siswa_rapor_and_spp_pages(): void
    {
        $this->seedReport();
        $this->seedSpp();

        $this->actingAs($this->siswa)->get(route('siswa.rapor'))->assertOk()->assertSee('Matematika');
        $this->actingAs($this->siswa)->get(route('siswa.spp'))->assertOk()->assertSee('Lunas');
    }

    public function test_siswa_without_student_id_shows_placeholder(): void
    {
        $terpisah = User::factory()->create(['role' => 'siswa', 'name' => 'Tanpa Data']);

        $response = $this->actingAs($terpisah)->get(route('siswa.dashboard'));

        $response->assertOk();
        $response->assertSee('belum terhubung ke data siswa');
    }

    public function test_other_roles_cannot_access_siswa_portal(): void
    {
        $roles = ['guru', 'orang_tua', 'super_admin', 'bendahara'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('siswa.dashboard'))->assertForbidden();
        }
    }
}
