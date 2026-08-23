<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\ScheduleCell;
use App\Models\ScheduleModel;
use App\Models\ScheduleModelGradeLevel;
use App\Models\ScheduleSlot;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalPenyusunanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Guru Uji']);
        $this->guru2 = User::factory()->create(['role' => 'guru', 'name' => 'Guru Dua']);
        $this->mapel = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'sort_order' => 1]);
        $this->classA = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $this->classB = ClassGroup::create(['name' => 'I-B', 'grade_level' => 'I']);
        $this->model = ScheduleModel::create([
            'academic_year_id' => $this->tahun->id,
            'name' => 'Model Uji',
            'start_time' => '07:00',
            'max_hours_per_day' => 3,
            'is_active' => true,
        ]);
        ScheduleModelGradeLevel::create(['schedule_model_id' => $this->model->id, 'grade_level' => 'I']);
        ScheduleSlot::create(['schedule_model_id' => $this->model->id, 'period_no' => 1, 'start_time' => '07:00', 'end_time' => '07:35', 'is_break' => false]);
        ScheduleSlot::create(['schedule_model_id' => $this->model->id, 'period_no' => 2, 'start_time' => '07:35', 'end_time' => '08:10', 'is_break' => false]);
    }

    protected function cellPayload(int $classId, string $day, int $period, ?int $teacherId, ?int $subjectId): array
    {
        return [
            'cells' => [
                ['class_group_id' => $classId, 'day' => $day, 'period_no' => $period, 'teacher_id' => $teacherId, 'subject_id' => $subjectId],
            ],
        ];
    }

    public function test_admin_can_save_cell(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jadwal.penyusunan.store', $this->model), $this->cellPayload($this->classA->id, 'senin', 1, $this->guru->id, $this->mapel->id));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('schedule_cells', [
            'class_group_id' => $this->classA->id,
            'day' => 'senin',
            'period_no' => 1,
            'teacher_id' => $this->guru->id,
            'subject_id' => $this->mapel->id,
        ]);
    }

    public function test_teacher_conflict_across_classes_is_rejected(): void
    {
        // Guru sama di kelas A senin 1 — simpan dulu
        ScheduleCell::create([
            'schedule_model_id' => $this->model->id,
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $this->classA->id,
            'day' => 'senin',
            'period_no' => 1,
            'teacher_id' => $this->guru->id,
            'subject_id' => $this->mapel->id,
        ]);

        // Coba isi guru sama di kelas B senin 1
        $response = $this->actingAs($this->admin)->post(route('jadwal.penyusunan.store', $this->model), $this->cellPayload($this->classB->id, 'senin', 1, $this->guru->id, $this->mapel->id));

        $response->assertSessionHasErrors('cells');
        $this->assertDatabaseMissing('schedule_cells', ['class_group_id' => $this->classB->id, 'day' => 'senin', 'period_no' => 1]);
    }

    public function test_teacher_can_teach_same_slot_different_class_different_time(): void
    {
        // Guru sama di kelas A senin 1, dan di kelas B SENIN 2 — tidak konflik (jam beda)
        ScheduleCell::create([
            'schedule_model_id' => $this->model->id,
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $this->classA->id,
            'day' => 'senin',
            'period_no' => 1,
            'teacher_id' => $this->guru->id,
            'subject_id' => $this->mapel->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('jadwal.penyusunan.store', $this->model), $this->cellPayload($this->classB->id, 'senin', 2, $this->guru->id, $this->mapel->id));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('schedule_cells', ['class_group_id' => $this->classB->id, 'day' => 'senin', 'period_no' => 2]);
    }

    public function test_generate_blank_creates_empty_cells(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jadwal.generate', $this->model), ['mode' => 'blank']);

        $response->assertSessionHasNoErrors();
        // 2 rombel x 6 hari x 2 slot = 24 sel
        $this->assertSame(24, ScheduleCell::where('schedule_model_id', $this->model->id)->count());
    }

    public function test_generate_does_not_overwrite_existing_data(): void
    {
        ScheduleCell::create([
            'schedule_model_id' => $this->model->id,
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $this->classA->id,
            'day' => 'senin',
            'period_no' => 1,
            'teacher_id' => $this->guru->id,
            'subject_id' => $this->mapel->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('jadwal.generate', $this->model), ['mode' => 'blank']);

        $response->assertSessionHasErrors('generate');
        $this->assertSame(1, ScheduleCell::where('schedule_model_id', $this->model->id)->count());
    }

    public function test_generate_copy_from_previous_year(): void
    {
        $tahunLama = AcademicYear::create(['name' => '2025/2026', 'semester' => 'ganjil', 'is_active' => false]);

        // Sel di model yang SAMA pada tahun lama
        ScheduleCell::create([
            'schedule_model_id' => $this->model->id,
            'academic_year_id' => $tahunLama->id,
            'class_group_id' => $this->classA->id,
            'day' => 'senin',
            'period_no' => 1,
            'teacher_id' => $this->guru->id,
            'subject_id' => $this->mapel->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('jadwal.generate', $this->model), [
            'mode' => 'copy',
            'source_academic_year_id' => $tahunLama->id,
        ]);

        $response->assertSessionHasNoErrors();
        $copied = ScheduleCell::where('schedule_model_id', $this->model->id)
            ->where('academic_year_id', $this->tahun->id)
            ->where('class_group_id', $this->classA->id)->where('day', 'senin')->where('period_no', 1)
            ->first();
        $this->assertNotNull($copied);
        $this->assertSame($this->guru->id, $copied->teacher_id);
    }
}
