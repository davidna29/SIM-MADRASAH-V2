<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ScheduleModel;
use App\Models\ScheduleModelGradeLevel;
use App\Models\ScheduleSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalModelModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Kurikulum Kelas I',
            'academic_year_id' => AcademicYear::active()->id,
            'start_time' => '07:00',
            'max_hours_per_day' => 6,
            'slot_duration' => 35,
            'grade_levels' => ['I'],
            'is_active' => '1',
        ], $overrides);
    }

    public function test_admin_can_create_schedule_model_with_slots_and_levels(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jadwal.model.store'), $this->payload());

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('schedule_models', ['name' => 'Kurikulum Kelas I']);
        $model = ScheduleModel::where('name', 'Kurikulum Kelas I')->first();
        $this->assertSame(6, $model->slots()->count());
        $this->assertSame(['I'], $model->gradeLevels());
    }

    public function test_active_model_with_overlapping_level_is_rejected(): void
    {
        $model = ScheduleModel::create([
            'academic_year_id' => AcademicYear::active()->id,
            'name' => 'Existing V–VI',
            'start_time' => '07:00',
            'max_hours_per_day' => 8,
            'is_active' => true,
        ]);
        ScheduleModelGradeLevel::create(['schedule_model_id' => $model->id, 'grade_level' => 'V']);
        ScheduleModelGradeLevel::create(['schedule_model_id' => $model->id, 'grade_level' => 'VI']);

        $response = $this->actingAs($this->admin)->post(route('jadwal.model.store'), $this->payload([
            'name' => 'Overlap',
            'grade_levels' => ['V'],
        ]));

        $response->assertSessionHasErrors('grade_levels');
        $this->assertDatabaseMissing('schedule_models', ['name' => 'Overlap']);
    }

    public function test_teacher_cannot_create_schedule_model(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        // Guru tak punya akses ke halaman admin model jadwal (route role:super_admin)
        $this->actingAs($guru)->get(route('jadwal.model.index'))->assertForbidden();
        $this->actingAs($guru)->post(route('jadwal.model.store'), $this->payload())->assertForbidden();
    }

    public function test_admin_can_update_schedule_model_levels(): void
    {
        $model = ScheduleModel::create([
            'academic_year_id' => AcademicYear::active()->id,
            'name' => 'Model',
            'start_time' => '07:00',
            'max_hours_per_day' => 6,
            'is_active' => true,
        ]);
        ScheduleModelGradeLevel::create(['schedule_model_id' => $model->id, 'grade_level' => 'I']);

        $response = $this->actingAs($this->admin)->put(route('jadwal.model.update', $model), [
            'name' => 'Model Diubah',
            'start_time' => '07:30',
            'max_hours_per_day' => 5,
            'grade_levels' => ['II', 'III'],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame(['II', 'III'], $model->fresh()->gradeLevels());
    }

    public function test_super_admin_can_delete_schedule_model(): void
    {
        $model = ScheduleModel::create([
            'academic_year_id' => AcademicYear::active()->id,
            'name' => 'Model',
            'start_time' => '07:00',
            'max_hours_per_day' => 6,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)->delete(route('jadwal.model.destroy', $model));

        $this->assertDatabaseMissing('schedule_models', ['id' => $model->id]);
    }
}
