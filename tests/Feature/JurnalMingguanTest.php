<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\TeachingJournal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalMingguanTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;

    protected ClassGroup $kelas;

    protected TeacherAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Umar Hakim']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $tahun = AcademicYear::active();

        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika']);

        $this->assignment = TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $this->kelas->id,
            'subject_id' => $mat->id,
            'user_id' => $this->guru->id,
        ]);
    }

    protected function makeStudent(string $nis, string $gender): StudentEnrollment
    {
        $student = Student::create(['nis' => $nis, 'name' => 'Siswa '.$nis, 'gender' => $gender]);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function makeJournal(string $materi, string $status, ?int $period = 1): TeachingJournal
    {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);

        return TeachingJournal::create([
            'academic_year_id' => $this->assignment->academic_year_id,
            'teacher_assignment_id' => $this->assignment->id,
            'journal_date' => $monday->toDateString(),
            'period_no' => $period,
            'materi' => $materi,
            'status' => $status,
            'recorded_by' => $this->guru->id,
        ]);
    }

    public function test_all_authorized_roles_can_view(): void
    {
        $roles = ['guru', 'tata_usaha', 'wakamad_kurikulum', 'kepala_madrasah', 'super_admin'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get(route('jurnal.admin.mingguan'));

            $response->assertOk();
        }
    }

    public function test_orang_tua_cannot_access(): void
    {
        $ortu = User::factory()->create(['role' => 'orang_tua']);

        $this->actingAs($ortu)->get(route('jurnal.admin.mingguan'))->assertForbidden();
    }

    public function test_filled_entries_are_shown_and_drafts_are_not(): void
    {
        $this->makeJournal('Materi sudah tercatat', 'terisi');
        $this->makeJournal('Materi masih draf', 'draft');

        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan', ['class_group_id' => $this->kelas->id]));

        $response->assertOk();
        $response->assertSee('Materi sudah tercatat');
        $response->assertDontSee('Materi masih draf');
    }

    public function test_empty_day_shows_placeholder(): void
    {
        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan', ['class_group_id' => $this->kelas->id]));

        $response->assertOk();
        $response->assertSee('Belum ada jurnal terisi');
        $response->assertSee('Senin');
        $response->assertSee('Sabtu');
    }

    public function test_gender_counts_from_active_enrollments(): void
    {
        $this->makeStudent('251001', 'L');
        $this->makeStudent('251002', 'P');
        $this->makeStudent('251003', 'P');

        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan', ['class_group_id' => $this->kelas->id]));

        $response->assertOk();
        $response->assertSee('1 L · 2 P');
    }

    public function test_entries_are_grouped_by_day_and_ordered_by_period(): void
    {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $this->makeJournal('Jam kedua', 'terisi', 2);
        $this->makeJournal('Jam pertama', 'terisi', 1);

        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan', ['class_group_id' => $this->kelas->id]));

        $response->assertOk();

        $html = $response->getContent();
        $posFirst = strpos($html, 'Jam pertama');
        $posSecond = strpos($html, 'Jam kedua');

        $this->assertTrue($posFirst !== false && $posSecond !== false && $posFirst < $posSecond);
        $this->assertTrue(str_contains($html, $monday->translatedFormat('j')));
    }
}
