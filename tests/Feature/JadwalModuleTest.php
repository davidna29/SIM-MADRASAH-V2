<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->class = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->subject = Subject::create(['code' => 'MAT', 'name' => 'Matematika']);
        $this->assignment = TeacherAssignment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'user_id' => $this->guru->id,
        ]);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'teacher_assignment_id' => $this->assignment->id,
            'day' => 'senin',
            'start_time' => '07:00',
            'end_time' => '08:00',
            'room' => 'Ruang 1',
        ], $overrides);
    }

    public function test_admin_can_create_schedule(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jadwal.store'), $this->payload());

        $response->assertRedirect(route('jadwal.index'));

        $this->assertDatabaseHas('schedules', [
            'teacher_assignment_id' => $this->assignment->id,
            'day' => 'senin',
            'room' => 'Ruang 1',
        ]);
    }

    public function test_end_time_must_be_after_start_time(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jadwal.store'), $this->payload([
            'start_time' => '09:00',
            'end_time' => '08:00',
        ]));

        $response->assertSessionHasErrors('end_time');
    }

    public function test_invalid_day_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jadwal.store'), $this->payload(['day' => 'minggu']));

        $response->assertSessionHasErrors('day');
    }

    public function test_teacher_cannot_manage_schedules(): void
    {
        // Guru tak punya akses ke halaman admin jadwal (route role:super_admin)
        $this->actingAs($this->guru)->get(route('jadwal.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('jadwal.create'))->assertForbidden();
    }

    public function test_schedule_grid_lists_class_schedules(): void
    {
        Schedule::create($this->payload() + ['academic_year_id' => AcademicYear::active()->id]);

        $response = $this->actingAs($this->admin)->get(route('jadwal.index', ['class_group_id' => $this->class->id]));

        $response->assertOk();
        $response->assertSee('Matematika');
        $response->assertSee('Ruang 1');
    }

    public function test_super_admin_can_delete_schedule(): void
    {
        $schedule = Schedule::create($this->payload() + ['academic_year_id' => AcademicYear::active()->id]);

        $this->actingAs($this->admin)->delete(route('jadwal.destroy', $schedule));

        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}
