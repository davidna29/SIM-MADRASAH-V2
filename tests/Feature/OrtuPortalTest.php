<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrtuPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $ortu;

    protected ClassGroup $kelas;

    protected StudentEnrollment $enrollment;

    protected Student $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ortu = User::factory()->create(['role' => 'orang_tua', 'name' => 'Ibu Aisyah']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $this->siswa = $this->makeStudent('251001', 'Aisyah');
        $guardian = Guardian::create(['user_id' => $this->ortu->id, 'name' => 'Ibu Aisyah']);
        $guardian->students()->attach($this->siswa->id);
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
            'student_id' => $this->siswa->id,
            'academic_year_id' => $tahun->id,
            'semester' => $tahun->semester,
            'version' => 1,
            'status' => 'terbit',
            'snapshot' => [
                'tahun' => $tahun->name,
                'semester' => $tahun->semester,
                'nis' => $this->siswa->nis,
                'siswa' => $this->siswa->name,
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
            'recorded_by' => $this->ortu->id,
        ]);
        Attendance::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $this->kelas->id,
            'student_enrollment_id' => $this->enrollment->id,
            'attendance_date' => '2026-08-05',
            'status' => 'sakit',
            'recorded_by' => $this->ortu->id,
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
            'recorded_by' => $this->ortu->id,
        ]);
    }

    public function test_ortu_can_view_own_child_ringkasan(): void
    {
        $this->seedReport();
        $this->seedAttendance();
        $this->seedSpp();

        $response = $this->actingAs($this->ortu)->get(route('ortu.ringkasan', $this->siswa));

        $response->assertOk();
        $response->assertSee('Matematika');
        $response->assertSee('85');
        $response->assertSee('Juli');
        $response->assertSee('Agustus');
        $response->assertSee('1 dari 6 bulan lunas');
    }

    public function test_ortu_cannot_view_other_child_ringkasan(): void
    {
        $anakLain = $this->makeStudent('251002', 'Budi');

        $this->actingAs($this->ortu)->get(route('ortu.ringkasan', $anakLain))->assertForbidden();
    }

    public function test_ringkasan_without_data_shows_placeholders(): void
    {
        $response = $this->actingAs($this->ortu)->get(route('ortu.ringkasan', $this->siswa));

        $response->assertOk();
        $response->assertSee('Belum ada nilai tercatat.');
        $response->assertSee('Belum ada pembayaran tercatat');
    }

    public function test_dashboard_lists_ringkasan_button(): void
    {
        $response = $this->actingAs($this->ortu)->get(route('ortu.dashboard'));

        $response->assertOk();
        $response->assertSee('Aisyah');
        $response->assertSee('Buka Ringkasan');
    }
}
