<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceReview;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceRekapBulananTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $guru;

    protected User $waliKelas;

    protected User $kepala;

    protected User $wakamadKesiswaan;

    protected User $wakamadKurikulum;

    protected ClassGroup $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->waliKelas = User::factory()->create(['role' => 'wali_kelas']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->wakamadKesiswaan = User::factory()->create(['role' => 'wakamad_kesiswaan']);
        $this->wakamadKurikulum = User::factory()->create(['role' => 'wakamad_kurikulum']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
    }

    protected function makeStudent(string $nis, string $name): StudentEnrollment
    {
        $person = Person::create(['nik' => '35'.str_pad($nis, 12, '0'), 'name' => $name, 'gender' => 'L', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => $name, 'gender' => 'L']);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function review(string $date): void
    {
        AttendanceReview::create([
            'class_group_id' => $this->kelas->id,
            'academic_year_id' => AcademicYear::active()->id,
            'attendance_date' => $date,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);
    }

    protected function record(StudentEnrollment $enrollment, string $date, string $status): void
    {
        Attendance::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_enrollment_id' => $enrollment->id,
            'attendance_date' => $date,
            'status' => $status,
            'recorded_by' => $this->admin->id,
        ]);
    }

    protected function pastDate(): string
    {
        return now()->copy()->subDay()->toDateString();
    }

    public function test_unreviewed_days_show_empty_cell(): void
    {
        $this->makeStudent('251001', 'Zaki');

        $response = $this->actingAs($this->admin)->get(route('kehadiran.rekap', [
            'class_group_id' => $this->kelas->id,
            'month' => now()->format('Y-m'),
        ]));

        $response->assertOk();
        $response->assertSee('Zaki');
        $response->assertSee('–');
        $response->assertDontSee('•');
    }

    public function test_reviewed_days_show_marks_and_tally(): void
    {
        $enrollment = $this->makeStudent('251002', 'Budi');
        $month = now()->startOfMonth();
        $day1 = $month->copy()->day(1)->toDateString();
        $day2 = $month->copy()->day(2)->toDateString();
        $day3 = $month->copy()->day(3)->toDateString();

        $this->review($day1);
        $this->review($day2);
        $this->review($day3);
        $this->record($enrollment, $day1, 'hadir');
        $this->record($enrollment, $day2, 'sakit');
        $this->record($enrollment, $day3, 'alpha');

        $response = $this->actingAs($this->admin)->get(route('kehadiran.rekap', [
            'class_group_id' => $this->kelas->id,
            'month' => $month->format('Y-m'),
        ]));

        $response->assertOk();
        $response->assertSee('•');
        // 1 hadir dari 3 hari efektif = 33.3%
        $response->assertSee('33.3%');
        // ketidakhadiran = 2 dari 3 slot = 66.7%
        $response->assertSee('66.7%');
    }

    public function test_non_privileged_cannot_access_past_date(): void
    {
        $enrollment = $this->makeStudent('251003', 'Citra');

        $this->actingAs($this->guru)->get(route('kehadiran.index', ['date' => $this->pastDate()]))->assertForbidden();
        $this->actingAs($this->waliKelas)->get(route('kehadiran.index', ['date' => $this->pastDate()]))->assertForbidden();

        $payload = [
            'attendance_date' => $this->pastDate(),
            'attendances' => [$enrollment->id => ['status' => 'hadir']],
        ];

        $this->actingAs($this->guru)->post(route('kehadiran.store'), $payload)->assertForbidden();
        $this->actingAs($this->waliKelas)->post(route('kehadiran.store'), $payload)->assertForbidden();
    }

    public function test_privileged_roles_can_open_and_edit_past_date(): void
    {
        $enrollment = $this->makeStudent('251004', 'Dewi');

        foreach ([$this->kepala, $this->wakamadKesiswaan, $this->wakamadKurikulum, $this->admin] as $user) {
            $this->actingAs($user)->get(route('kehadiran.index', ['date' => $this->pastDate()]))->assertOk();

            $response = $this->actingAs($user)->post(route('kehadiran.store'), [
                'attendance_date' => $this->pastDate(),
                'attendances' => [$enrollment->id => ['status' => 'hadir']],
            ]);

            $response->assertSessionHasNoErrors();
        }
    }

    public function test_resubmit_same_day_does_not_duplicate_review(): void
    {
        $enrollment = $this->makeStudent('251005', 'Eko');
        $today = now()->toDateString();

        $payload = [
            'attendance_date' => $today,
            'attendances' => [$enrollment->id => ['status' => 'hadir']],
        ];

        $this->actingAs($this->admin)->post(route('kehadiran.store'), $payload);
        $this->actingAs($this->admin)->post(route('kehadiran.store'), [
            'attendance_date' => $today,
            'attendances' => [$enrollment->id => ['status' => 'alpha']],
        ]);

        $this->assertSame(1, AttendanceReview::where('class_group_id', $this->kelas->id)->where('attendance_date', $today)->count());
        $this->assertDatabaseHas('attendance_reviews', [
            'class_group_id' => $this->kelas->id,
            'attendance_date' => $today,
        ]);
    }

    public function test_summary_row_counts_are_correct(): void
    {
        $budi = $this->makeStudent('251006', 'Budi');
        $citra = $this->makeStudent('251007', 'Citra');
        $month = now()->startOfMonth();
        $day1 = $month->copy()->day(1)->toDateString();
        $day2 = $month->copy()->day(2)->toDateString();

        $this->review($day1);
        $this->review($day2);
        $this->record($budi, $day1, 'hadir');
        $this->record($budi, $day2, 'izin');
        $this->record($citra, $day1, 'sakit');
        $this->record($citra, $day2, 'hadir');

        // Total S=1, I=1, A=0, ketidakhadiran=2; hadir=2; slot=2 siswa x 2 hari=4
        // kehadiran=50%, ketidakhadiran=50%
        $response = $this->actingAs($this->admin)->get(route('kehadiran.rekap', [
            'class_group_id' => $this->kelas->id,
            'month' => $month->format('Y-m'),
        ]));

        $response->assertOk();
        $response->assertSee('50%');
        $response->assertSee('2'); // total ketidakhadiran di footer
        $response->assertSee('Hari efektif');
    }

    public function test_no_reviewed_days_returns_dash_without_error(): void
    {
        $this->makeStudent('251008', 'Fajar');

        $response = $this->actingAs($this->admin)->get(route('kehadiran.rekap', [
            'class_group_id' => $this->kelas->id,
            'month' => now()->format('Y-m'),
        ]));

        $response->assertOk();
        $response->assertSee('Hari efektif bulan ini:');
        $response->assertSee('0 hari');
    }
}
