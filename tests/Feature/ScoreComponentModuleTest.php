<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Report;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\ScoreComponentValue;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreComponentModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $guru;

    protected Subject $mat;

    protected ClassGroup $kelas;

    protected TeacherAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kurikulum']);
        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Umar']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $this->mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'sort_order' => 1]);

        $this->assignment = TeacherAssignment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'subject_id' => $this->mat->id,
            'user_id' => $this->guru->id,
        ]);
    }

    protected function makeStudent(string $nis, string $name): StudentEnrollment
    {
        $student = Student::create(['nis' => $nis, 'name' => $name, 'gender' => 'L']);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function createComponents(array $weights): void
    {
        $tahun = AcademicYear::active();

        foreach ($weights as $i => $weight) {
            ScoreComponent::create([
                'academic_year_id' => $tahun->id,
                'name' => 'Komponen '.($i + 1),
                'weight' => $weight,
                'sort_order' => $i + 1,
            ]);
        }
    }

    // ==== Konfigurasi Komponen ====

    public function test_wakamad_can_open_component_page(): void
    {
        $this->createComponents([40, 60]);

        $this->actingAs($this->wakamad)->get(route('akademik.komponen-nilai.index'))
            ->assertOk()
            ->assertSee('Komponen 1')
            ->assertSee('Komponen 2')
            ->assertSee('40');
        $this->actingAs($this->admin)->get(route('akademik.komponen-nilai.index'))->assertOk();
    }

    public function test_teacher_cannot_manage_components(): void
    {
        $this->actingAs($this->guru)->get(route('akademik.komponen-nilai.index'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('akademik.komponen-nilai.store'), [
            'name' => 'Tugas', 'weight' => 100,
        ])->assertForbidden();
    }

    public function test_store_component_with_total_weight_100(): void
    {
        $this->createComponents([40, 30]);

        $response = $this->actingAs($this->wakamad)->post(route('akademik.komponen-nilai.store'), [
            'name' => 'PAS', 'weight' => 30,
        ]);

        $response->assertRedirect(route('akademik.komponen-nilai.index'));
        $this->assertDatabaseHas('score_components', ['name' => 'PAS', 'weight' => 30]);
    }

    public function test_store_component_with_total_weight_not_100_is_rejected(): void
    {
        $this->createComponents([40, 30]);

        $response = $this->actingAs($this->wakamad)->post(route('akademik.komponen-nilai.store'), [
            'name' => 'PAS', 'weight' => 20,
        ]);

        $response->assertSessionHasErrors('weight');
        $this->assertDatabaseMissing('score_components', ['name' => 'PAS']);
    }

    public function test_duplicate_component_name_is_rejected(): void
    {
        $this->createComponents([40, 60]);

        $response = $this->actingAs($this->wakamad)->post(route('akademik.komponen-nilai.store'), [
            'name' => 'Komponen 1', 'weight' => 0,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_component_keeps_total_100(): void
    {
        $this->createComponents([40, 30, 30]);
        $component = ScoreComponent::where('name', 'Komponen 1')->first();

        // Ubah nama dan bobot tetap 40 agar total tetap 100 (40+30+30).
        $response = $this->actingAs($this->wakamad)->put(route('akademik.komponen-nilai.update', $component), [
            'name' => 'Penilaian Harian', 'weight' => 40,
        ]);

        $response->assertRedirect(route('akademik.komponen-nilai.index'));
        $this->assertDatabaseHas('score_components', ['name' => 'Penilaian Harian', 'weight' => 40]);
    }

    public function test_destroy_component_in_use_is_rejected(): void
    {
        $this->createComponents([40, 60]);
        $component = ScoreComponent::where('name', 'Komponen 1')->first();
        $enrollment = $this->makeStudent('251001', 'Aisyah');

        $score = Score::create([
            'student_enrollment_id' => $enrollment->id,
            'subject_id' => $this->mat->id,
            'academic_year_id' => AcademicYear::active()->id,
            'semester' => AcademicYear::active()->semester,
            'score' => 80,
        ]);

        ScoreComponentValue::create([
            'score_id' => $score->id,
            'score_component_id' => $component->id,
            'value' => 80,
        ]);

        $response = $this->actingAs($this->wakamad)->delete(route('akademik.komponen-nilai.destroy', $component));

        $response->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('score_components', ['id' => $component->id]);
    }

    // ==== Halaman Input Nilai ====

    public function test_guru_can_open_edit_page_with_components(): void
    {
        $this->createComponents([40, 60]);
        $this->makeStudent('251000', 'Zahra');

        $response = $this->actingAs($this->guru)->get(route('guru.nilai.edit', $this->assignment));

        $response->assertOk();
        $response->assertSee('Komponen 1');
        $response->assertSee('Komponen 2');
    }

    public function test_guru_can_open_edit_page_legacy_without_components(): void
    {
        $this->makeStudent('251000', 'Zahra');

        $response = $this->actingAs($this->guru)->get(route('guru.nilai.edit', $this->assignment));

        $response->assertOk();
        $response->assertSee('Nilai');
    }

    // ==== Input Nilai per Komponen ====

    public function test_guru_inputs_component_values_and_final_score_is_weighted(): void
    {
        $this->createComponents([40, 30, 30]);
        $components = ScoreComponent::orderBy('sort_order')->get();
        $enrollment = $this->makeStudent('251002', 'Bilal');

        $response = $this->actingAs($this->guru)->post(route('guru.nilai.update', $this->assignment), [
            'values' => [
                $enrollment->id => [
                    $components[0]->id => 80,
                    $components[1]->id => 90,
                    $components[2]->id => 70,
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // (80*0.4 + 90*0.3 + 70*0.3) = 32 + 27 + 21 = 80
        $score = Score::where('student_enrollment_id', $enrollment->id)->first();
        $this->assertSame(80, $score->score);
        $this->assertSame(3, $score->componentValues()->count());
    }

    public function test_guru_clears_values_deletes_score_when_all_empty(): void
    {
        $this->createComponents([40, 60]);
        $components = ScoreComponent::orderBy('sort_order')->get();
        $enrollment = $this->makeStudent('251003', 'Citra');

        $this->actingAs($this->guru)->post(route('guru.nilai.update', $this->assignment), [
            'values' => [
                $enrollment->id => [
                    $components[0]->id => 85,
                    $components[1]->id => 75,
                ],
            ],
        ]);

        $this->assertSame(1, Score::count());

        $this->actingAs($this->guru)->post(route('guru.nilai.update', $this->assignment), [
            'values' => [
                $enrollment->id => [
                    $components[0]->id => null,
                    $components[1]->id => null,
                ],
            ],
        ]);

        $this->assertSame(0, Score::count());
        $this->assertSame(0, ScoreComponentValue::count());
    }

    public function test_guru_without_assignment_cannot_input(): void
    {
        $this->createComponents([40, 60]);
        $otherGuru = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Imam']);
        $enrollment = $this->makeStudent('251004', 'Dimas');

        $this->actingAs($otherGuru)->post(route('guru.nilai.update', $this->assignment), [
            'values' => [$enrollment->id => []],
        ])->assertForbidden();
    }

    public function test_legacy_mode_without_components_still_works(): void
    {
        $enrollment = $this->makeStudent('251005', 'Eka');

        $response = $this->actingAs($this->guru)->post(route('guru.nilai.update', $this->assignment), [
            'scores' => [$enrollment->id => 88],
        ]);

        $response->assertSessionHas('status');
        $this->assertSame(88, Score::where('student_enrollment_id', $enrollment->id)->value('score'));
    }

    public function test_terbitkan_uses_final_score_from_components(): void
    {
        $this->createComponents([40, 60]);
        $components = ScoreComponent::orderBy('sort_order')->get();
        $enrollment = $this->makeStudent('251006', 'Fajar');

        $this->actingAs($this->guru)->post(route('guru.nilai.update', $this->assignment), [
            'values' => [
                $enrollment->id => [
                    $components[0]->id => 80,
                    $components[1]->id => 90,
                ],
            ],
        ]);

        $this->actingAs($this->guru)->post(route('guru.nilai.terbitkan', $this->assignment))->assertRedirect(route('guru.penugasan'));

        $report = Report::first();
        $this->assertNotNull($report);
        $this->assertSame(86, $report->items()->first()->score); // (80*0.4 + 90*0.6) = 32 + 54 = 86
    }
}
