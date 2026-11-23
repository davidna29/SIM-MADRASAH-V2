<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected AcademicYear $year;

    protected ClassGroup $classGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->classGroup = ClassGroup::create([
            'name' => 'I-A',
            'grade_level' => 'I',
        ]);
    }

    protected function apiUser(string $role, array $attributes = []): User
    {
        return User::factory()->create([
            'role' => $role,
            'must_change_password' => false,
            'is_active' => true,
            ...$attributes,
        ]);
    }

    protected function actingAsApi(User $user, array $abilities = ['website:read']): void
    {
        $this->app['auth']->forgetGuards();
        Sanctum::actingAs($user, $abilities);
    }

    protected function bearer(User $user, array $abilities = ['website:read']): string
    {
        $this->app['auth']->forgetGuards();

        return $user->createToken('api-test', $abilities, now()->addDay())->plainTextToken;
    }

    /**
     * @return array<string, string>
     */
    protected function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    protected function makeStudent(string $nis, string $name, ?ClassGroup $classGroup = null, string $status = 'aktif'): StudentEnrollment
    {
        $person = Person::create([
            'nik' => '35'.str_pad($nis, 14, '0'),
            'name' => $name,
            'gender' => 'L',
            'religion' => 'Islam',
            'phone' => '081200000000',
            'address' => 'Jalan Rahasia',
        ]);

        $student = Student::create([
            'person_id' => $person->id,
            'nis' => $nis,
            'name' => $name,
            'gender' => 'L',
        ]);

        return StudentEnrollment::create([
            'academic_year_id' => $this->year->id,
            'class_group_id' => ($classGroup ?? $this->classGroup)->id,
            'student_id' => $student->id,
            'status' => $status,
        ]);
    }

    protected function makeSubject(string $code, string $name): Subject
    {
        return Subject::create(['code' => $code, 'name' => $name, 'sort_order' => 1]);
    }

    protected function makeAssignment(User $teacher, Subject $subject, ?ClassGroup $classGroup = null): TeacherAssignment
    {
        return TeacherAssignment::create([
            'academic_year_id' => $this->year->id,
            'class_group_id' => ($classGroup ?? $this->classGroup)->id,
            'subject_id' => $subject->id,
            'user_id' => $teacher->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function idempotentHeaders(string $key, array $headers = []): array
    {
        return [...$headers, 'Idempotency-Key' => $key];
    }
}
