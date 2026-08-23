<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AkademikModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
    }

    // ==== Mata Pelajaran ====

    public function test_admin_can_create_subject(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mapel.store'), [
            'code' => 'FIQH',
            'name' => 'Fikih',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subjects', ['code' => 'FIQH']);
    }

    public function test_subject_code_must_be_unique(): void
    {
        Subject::create(['code' => 'MAT', 'name' => 'Matematika']);

        $response = $this->actingAs($this->admin)->post(route('mapel.store'), [
            'code' => 'MAT',
            'name' => 'Matematika Baru',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_teacher_cannot_manage_subjects(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->actingAs($teacher)->get(route('mapel.index'))->assertForbidden();
        $this->actingAs($teacher)->post(route('mapel.store'), [
            'code' => 'FIQH', 'name' => 'Fikih',
        ])->assertForbidden();
    }

    public function test_subject_destroy_is_blocked_when_in_use(): void
    {
        $subject = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'sort_order' => 1]);
        $class = ClassGroup::create(['name' => 'VII-A', 'grade_level' => 'VII']);
        $guru = User::factory()->create(['role' => 'guru']);

        \App\Models\TeacherAssignment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $class->id,
            'subject_id' => $subject->id,
            'user_id' => $guru->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('mapel.destroy', $subject));

        $response->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }

    public function test_subject_destroy_succeeds_when_unused(): void
    {
        $subject = Subject::create(['code' => 'SKI', 'name' => 'Sejarah Kebudayaan Islam', 'sort_order' => 2]);

        $response = $this->actingAs($this->admin)->delete(route('mapel.destroy', $subject));

        $response->assertRedirect(route('mapel.index'));
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_subject_reorder_updates_sort_order(): void
    {
        $a = Subject::create(['code' => 'AAA', 'name' => 'Mapel A', 'sort_order' => 1]);
        $b = Subject::create(['code' => 'BBB', 'name' => 'Mapel B', 'sort_order' => 2]);

        $response = $this->actingAs($this->admin)->post(route('mapel.reorder'), [
            'order' => [$b->id, $a->id],
        ]);

        $response->assertRedirect();
        $this->assertSame(1, $b->fresh()->sort_order);
        $this->assertSame(2, $a->fresh()->sort_order);
    }

    // ==== Kelas & Penempatan ====

    public function test_admin_can_create_class_group(): void
    {
        $response = $this->actingAs($this->admin)->post(route('kelas.store'), [
            'name' => 'VII-A',
            'grade_level' => 'VII',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_groups', ['name' => 'VII-A']);
    }

    public function test_class_name_must_be_unique(): void
    {
        ClassGroup::create(['name' => 'VII-A', 'grade_level' => 'VII']);

        $response = $this->actingAs($this->admin)->post(route('kelas.store'), [
            'name' => 'VII-A',
            'grade_level' => 'VII',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_place_student_into_class(): void
    {
        $class = ClassGroup::create(['name' => 'VII-A', 'grade_level' => 'VII']);
        $student = Student::create(['nis' => '240101', 'name' => 'Aisyah', 'gender' => 'P']);

        $response = $this->actingAs($this->admin)->post(route('kelas.place', $class), [
            'student_ids' => [$student->id],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $student->id,
            'class_group_id' => $class->id,
            'status' => 'aktif',
        ]);
    }

    public function test_place_rejects_student_already_in_another_class(): void
    {
        $tahun = AcademicYear::active();
        $classA = ClassGroup::create(['name' => 'VII-A', 'grade_level' => 'VII']);
        $classB = ClassGroup::create(['name' => 'VII-B', 'grade_level' => 'VII']);
        $student = Student::create(['nis' => '240101', 'name' => 'Aisyah', 'gender' => 'P']);

        StudentEnrollment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $classA->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->post(route('kelas.place', $classB), [
            'student_ids' => [$student->id],
        ]);

        $response->assertSessionHasErrors('student_ids');
    }

    public function test_unplace_marks_student_as_alumni(): void
    {
        $tahun = AcademicYear::active();
        $class = ClassGroup::create(['name' => 'VII-A', 'grade_level' => 'VII']);
        $student = Student::create(['nis' => '240101', 'name' => 'Aisyah', 'gender' => 'P']);

        $enrollment = StudentEnrollment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $this->actingAs($this->admin)->post(route('kelas.unplace', [$class, $enrollment]));

        $this->assertSame('alumni', $enrollment->fresh()->status);
    }
}
