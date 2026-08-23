<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenugasanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        ClassGroup::create(['name' => 'VII-A', 'grade_level' => 'VII']);
        Subject::create(['code' => 'MAT', 'name' => 'Matematika']);
    }

    protected function guru(): User
    {
        return User::factory()->create(['role' => 'guru']);
    }

    protected function payload(User $guru, array $overrides = []): array
    {
        return array_merge([
            'class_group_id' => ClassGroup::first()->id,
            'subject_id' => Subject::first()->id,
            'user_id' => $guru->id,
            'academic_year_id' => AcademicYear::active()->id,
        ], $overrides);
    }

    public function test_admin_can_create_assignment(): void
    {
        $guru = $this->guru();

        $response = $this->actingAs($this->admin)->post(route('penugasan.store'), $this->payload($guru));

        $response->assertRedirect(route('penugasan.index'));
        $this->assertDatabaseHas('teacher_assignments', ['user_id' => $guru->id]);
    }

    public function test_assignment_duplicate_is_rejected(): void
    {
        $guru = $this->guru();
        $payload = $this->payload($guru);

        TeacherAssignment::create($payload);

        $response = $this->actingAs($this->admin)
            ->from(route('penugasan.create'))
            ->post(route('penugasan.store'), $payload);

        $response->assertSessionHasErrors('class_group_id');
    }

    public function test_non_guru_user_cannot_be_assigned(): void
    {
        $ortu = User::factory()->create(['role' => 'orang_tua']);

        $response = $this->actingAs($this->admin)
            ->post(route('penugasan.store'), $this->payload($ortu));

        $response->assertSessionHasErrors('user_id');
    }

    public function test_teacher_cannot_manage_assignments(): void
    {
        $guru = $this->guru();

        $this->actingAs($guru)->get(route('penugasan.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('penugasan.create'))->assertForbidden();
    }

    public function test_admin_can_update_assignment(): void
    {
        $guru = $this->guru();
        $assignment = TeacherAssignment::create($this->payload($guru));
        $guru2 = $this->guru();

        $response = $this->actingAs($this->admin)->put(route('penugasan.update', $assignment), $this->payload($guru2));

        $response->assertRedirect(route('penugasan.index'));
        $this->assertSame($guru2->id, $assignment->fresh()->user_id);
    }

    public function test_super_admin_can_delete_assignment(): void
    {
        $guru = $this->guru();
        $assignment = TeacherAssignment::create($this->payload($guru));

        $this->actingAs($this->admin)->delete(route('penugasan.destroy', $assignment));

        $this->assertDatabaseMissing('teacher_assignments', ['id' => $assignment->id]);
    }
}
