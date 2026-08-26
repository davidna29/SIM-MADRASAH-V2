<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\HomeroomAssignment;
use App\Models\Offense;
use App\Models\Person;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortofolioModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $waliKelas;

    protected User $guru;

    protected AcademicYear $tahun;

    protected ClassGroup $kelas;

    protected Student $siswa;

    protected StudentEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kesiswaan']);
        $this->waliKelas = User::factory()->create(['role' => 'wali_kelas']);
        $this->guru = User::factory()->create(['role' => 'guru']);

        // Buat siswa
        $person = Person::create(['nik' => '3501010101010001', 'name' => 'Aisyah Putri', 'gender' => 'P', 'religion' => 'Islam']);
        $this->siswa = Student::create(['person_id' => $person->id, 'nis' => '240101', 'name' => 'Aisyah Putri', 'gender' => 'P']);

        $this->enrollment = StudentEnrollment::create([
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $this->siswa->id,
            'status' => 'aktif',
        ]);

        // Homeroom assignment untuk wali kelas
        HomeroomAssignment::create([
            'class_group_id' => $this->kelas->id,
            'academic_year_id' => $this->tahun->id,
            'user_id' => $this->waliKelas->id,
            'status' => 'aktif',
        ]);

        // Seed data portofolio
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK', 'sort_order' => 1]);
        $teacher = User::factory()->create(['role' => 'guru']);
        $assignment = TeacherAssignment::create([
            'user_id' => $teacher->id,
            'subject_id' => $subject->id,
            'class_group_id' => $this->kelas->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        Report::create([
            'student_id' => $this->siswa->id,
            'academic_year_id' => $this->tahun->id,
            'semester' => 'ganjil',
            'snapshot' => json_encode(['items' => []]),
            'status' => 'terbit',
        ]);

        Attendance::create([
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $this->kelas->id,
            'student_enrollment_id' => $this->enrollment->id,
            'attendance_date' => now()->subDays(5),
            'status' => 'hadir',
        ]);

        Achievement::create([
            'student_id' => $this->siswa->id,
            'nama_kegiatan' => 'Olimpiade Matematika',
            'jenis' => 'akademik',
            'tingkat' => 'sekolah',
            'status_verifikasi' => 'terverifikasi',
            'tanggal' => now()->subMonth(),
        ]);

        Offense::create([
            'student_id' => $this->siswa->id,
            'kategori' => 'Ketertiban',
            'tingkat' => 'ringan',
            'poin' => 10,
            'tanggal_kejadian' => now()->subWeek(),
            'kronologi' => 'Terlambat masuk kelas',
            'status_penyelesaian' => 'selesai',
        ]);
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('portofolio.index'));
        $response->assertOk();
    }

    public function test_wakamad_can_access_index(): void
    {
        $this->actingAs($this->wakamad);
        $response = $this->get(route('portofolio.index'));
        $response->assertOk();
    }

    public function test_guru_cannot_access(): void
    {
        $this->actingAs($this->guru);
        $response = $this->get(route('portofolio.index'));
        $response->assertForbidden();
    }

    public function test_can_search_student(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('portofolio.index', ['q' => 'Aisyah']));
        $response->assertOk();
        $response->assertSee('Aisyah Putri');
    }

    public function test_can_view_portofolio(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('portofolio.show', $this->siswa));
        $response->assertOk();
        $response->assertSee('Aisyah Putri');
        $response->assertSee('240101');
    }

    public function test_portofolio_shows_achievements(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('portofolio.show', $this->siswa));
        $response->assertOk();
        $response->assertSee('Olimpiade Matematika');
    }

    public function test_portofolio_shows_offenses(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('portofolio.show', $this->siswa));
        $response->assertOk();
        $response->assertSee('Ketertiban');
    }

    public function test_can_generate_qr(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('portofolio.qr', $this->siswa));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_wali_kelas_can_view_own_student(): void
    {
        $this->actingAs($this->waliKelas);
        $response = $this->get(route('portofolio.show', $this->siswa));
        $response->assertOk();
    }

    public function test_wali_kelas_cannot_view_other_class_student(): void
    {
        // Buat siswa lain di kelas berbeda
        $person2 = Person::create(['nik' => '3501010101010002', 'name' => 'Budi', 'gender' => 'L', 'religion' => 'Islam']);
        $siswa2 = Student::create(['person_id' => $person2->id, 'nis' => '240102', 'name' => 'Budi', 'gender' => 'L']);
        $kelas2 = ClassGroup::create(['name' => 'II-A', 'grade_level' => 'II']);
        StudentEnrollment::create([
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $kelas2->id,
            'student_id' => $siswa2->id,
            'status' => 'aktif',
        ]);

        $this->actingAs($this->waliKelas);
        $response = $this->get(route('portofolio.show', $siswa2));
        $response->assertForbidden();
    }
}
