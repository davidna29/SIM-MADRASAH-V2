<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected ClassGroup $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
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

    public function test_admin_roles_can_view_dashboard(): void
    {
        $roles = ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan', 'bendahara', 'tata_usaha'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('dashboard'))->assertOk();
        }
    }

    public function test_non_admin_roles_cannot_view_dashboard(): void
    {
        $roles = ['guru', 'orang_tua', 'siswa'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        }
    }

    public function test_dashboard_shows_real_data(): void
    {
        $enrollment = $this->makeStudent('251001', 'Aisyah');
        $tahun = AcademicYear::active();

        TuitionPayment::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $tahun->id,
            'semester' => $tahun->semester,
            'bulan' => 7,
            'nominal' => 100000,
            'status' => 'lunas',
            'tanggal_bayar' => now()->toDateString(),
            'metode' => 'tunai',
            'recorded_by' => $this->admin->id,
        ]);

        Attendance::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $this->kelas->id,
            'student_enrollment_id' => $enrollment->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
            'recorded_by' => $this->admin->id,
        ]);

        activity('keuangan')->by($this->admin)->log('spp_dibayar');

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Tahun '.$tahun->name);
        $response->assertSee('Aisyah');
        $response->assertSee('Kelas I-A belum mereview kehadiran hari ini');
        $response->assertSee('mencatat pembayaran SPP');
    }

    public function test_dashboard_without_data_still_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Tidak ada tindakan yang menunggu.');
        $response->assertSee('Belum ada pembayaran tercatat.');
    }
}
