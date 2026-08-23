<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'nis' => '251001',
            'nik' => '3518000101010001',
            'name' => 'Zaki Ramadhan',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_place' => 'Surabaya',
            'birth_date' => '2019-01-01',
            'phone' => '081234567890',
            'email' => 'zaki@example.com',
            'class_group_id' => ClassGroup::first()->id,
        ], $overrides);
    }

    public function test_admin_can_create_student_with_person_and_placement(): void
    {
        $response = $this->actingAs($this->admin)->post(route('siswa.store'), $this->payload());

        $response->assertRedirect();

        $this->assertDatabaseHas('people', ['nik' => '3518000101010001']);
        $this->assertDatabaseHas('students', ['nis' => '251001']);
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => Student::where('nis', '251001')->first()->id,
            'class_group_id' => ClassGroup::first()->id,
        ]);
    }

    public function test_nis_and_nik_must_be_unique(): void
    {
        $person = Person::create(['nik' => '3518000101010001', 'name' => 'Lama', 'gender' => 'L', 'religion' => 'Islam']);
        Student::create(['person_id' => $person->id, 'nis' => '251001', 'name' => 'Lama', 'gender' => 'L']);

        $response = $this->actingAs($this->admin)->post(route('siswa.store'), $this->payload());

        $response->assertSessionHasErrors(['nis', 'nik']);
    }

    public function test_invalid_nik_length_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('siswa.store'), $this->payload(['nik' => '123']));

        $response->assertSessionHasErrors('nik');
    }

    public function test_teacher_cannot_manage_students(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('siswa.index'))->assertForbidden();
        $this->actingAs($guru)->post(route('siswa.store'), $this->payload())->assertForbidden();
    }

    public function test_student_destroy_is_blocked_when_active(): void
    {
        $person = Person::create(['nik' => '3518000101010002', 'name' => 'Budi', 'gender' => 'L', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '251002', 'name' => 'Budi', 'gender' => 'L']);
        StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => ClassGroup::first()->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('siswa.destroy', $student));

        $response->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    public function test_admin_can_update_student_placement(): void
    {
        $person = Person::create(['nik' => '3518000101010003', 'name' => 'Citra', 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '251003', 'name' => 'Citra', 'gender' => 'P']);

        $response = $this->actingAs($this->admin)->put(route('siswa.update', $student), $this->payload([
            'nis' => '251003',
            'nik' => '3518000101010003',
            'name' => 'Citra Ayu',
        ]));

        $response->assertRedirect();
        $this->assertSame('Citra Ayu', $student->fresh()->person->name);
        $this->assertDatabaseHas('student_enrollments', ['student_id' => $student->id]);
    }
}
