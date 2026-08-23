<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KehadiranModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->class = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
    }

    protected function makeStudent(string $nis, string $name): StudentEnrollment
    {
        $person = Person::create(['nik' => '35' . str_pad($nis, 12, '0'), 'name' => $name, 'gender' => 'L', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => $name, 'gender' => 'L']);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->class->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_record_attendance(): void
    {
        $enrollment = $this->makeStudent('251001', 'Zaki');

        $response = $this->actingAs($this->admin)->post(route('kehadiran.store'), [
            'attendance_date' => '2026-08-25',
            'attendances' => [
                $enrollment->id => ['status' => 'sakit', 'note' => 'Demam'],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendances', [
            'student_enrollment_id' => $enrollment->id,
            'attendance_date' => '2026-08-25',
            'status' => 'sakit',
            'note' => 'Demam',
        ]);
    }

    public function test_attendance_is_unique_per_student_per_day(): void
    {
        $enrollment = $this->makeStudent('251002', 'Budi');

        $payload = [
            'attendance_date' => '2026-08-25',
            'attendances' => [$enrollment->id => ['status' => 'hadir']],
        ];

        $this->actingAs($this->admin)->post(route('kehadiran.store'), $payload);
        $this->actingAs($this->admin)->post(route('kehadiran.store'), array_merge($payload, [
            'attendances' => [$enrollment->id => ['status' => 'alpha']],
        ]));

        // updateOrCreate — tetap satu baris, status diperbarui
        $this->assertSame(1, Attendance::where('student_enrollment_id', $enrollment->id)->where('attendance_date', '2026-08-25')->count());
        $this->assertSame('alpha', Attendance::where('student_enrollment_id', $enrollment->id)->where('attendance_date', '2026-08-25')->first()->status);
    }

    public function test_teacher_cannot_access_attendance(): void
    {
        $ortu = User::factory()->create(['role' => 'orang_tua']);

        $this->actingAs($ortu)->get(route('kehadiran.index'))->assertForbidden();
    }

    public function test_attendance_page_lists_class_students(): void
    {
        $enrollment = $this->makeStudent('251003', 'Citra');

        $response = $this->actingAs($this->admin)->get(route('kehadiran.index', [
            'class_group_id' => $this->class->id,
            'date' => '2026-08-25',
        ]));

        $response->assertOk();
        $response->assertSee('Citra');
    }
}
