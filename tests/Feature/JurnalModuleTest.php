<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\TeachingJournal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;

    protected User $guruLain;

    protected TeacherAssignment $assignment;

    protected TeacherAssignment $assignmentLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Umar Hakim']);
        $this->guruLain = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Imam Syafii']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $tahun = AcademicYear::active();

        $kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika']);
        $ipa = Subject::create(['code' => 'IPA', 'name' => 'IPA']);

        $this->assignment = TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $kelas->id,
            'subject_id' => $mat->id,
            'user_id' => $this->guru->id,
        ]);

        $this->assignmentLain = TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $kelas->id,
            'subject_id' => $ipa->id,
            'user_id' => $this->guruLain->id,
        ]);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'journal_date' => now()->toDateString(),
            'period_no' => '2',
            'materi' => 'Membilang bilangan 1 sampai 20',
            'tujuan' => 'Siswa dapat membilang dengan benar.',
            'metode' => 'Ceramah interaktif',
            'catatan' => 'Kelas kondusif.',
            'tindak_lanjut' => 'Latihan di rumah.',
            'status' => 'terisi',
        ], $overrides);
    }

    public function test_guru_can_create_journal_for_own_assignment(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal.store', $this->assignment), $this->payload());

        $response->assertRedirect(route('guru.jurnal.show', $this->assignment));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('teaching_journals', [
            'teacher_assignment_id' => $this->assignment->id,
            'materi' => 'Membilang bilangan 1 sampai 20',
            'status' => 'terisi',
            'recorded_by' => $this->guru->id,
        ]);
    }

    public function test_guru_cannot_create_journal_for_others_assignment(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal.store', $this->assignmentLain), $this->payload());

        $response->assertForbidden();
        $this->assertDatabaseCount('teaching_journals', 0);
    }

    public function test_journal_is_unique_per_assignment_date_and_period(): void
    {
        $this->actingAs($this->guru)->post(route('guru.jurnal.store', $this->assignment), $this->payload());

        $response = $this->actingAs($this->guru)->post(route('guru.jurnal.store', $this->assignment), $this->payload());

        $response->assertSessionHasErrors('journal_date');
        $this->assertSame(1, TeachingJournal::where('teacher_assignment_id', $this->assignment->id)->count());
    }

    public function test_draft_saves_with_draft_status(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal.store', $this->assignment), $this->payload([
            'status' => 'draft',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teaching_journals', [
            'teacher_assignment_id' => $this->assignment->id,
            'status' => 'draft',
        ]);
    }

    public function test_guru_can_edit_own_journal(): void
    {
        $journal = TeachingJournal::create([
            'academic_year_id' => $this->assignment->academic_year_id,
            'teacher_assignment_id' => $this->assignment->id,
            'journal_date' => now()->toDateString(),
            'period_no' => 1,
            'materi' => 'Materi lama',
            'status' => 'draft',
            'recorded_by' => $this->guru->id,
        ]);

        $response = $this->actingAs($this->guru)->put(route('guru.jurnal.update', [$this->assignment, $journal]), $this->payload([
            'materi' => 'Materi baru',
        ]));

        $response->assertRedirect(route('guru.jurnal.show', $this->assignment));
        $this->assertDatabaseHas('teaching_journals', [
            'id' => $journal->id,
            'materi' => 'Materi baru',
            'status' => 'terisi',
        ]);
    }

    public function test_guru_cannot_edit_others_journal(): void
    {
        $journal = TeachingJournal::create([
            'academic_year_id' => $this->assignmentLain->academic_year_id,
            'teacher_assignment_id' => $this->assignmentLain->id,
            'journal_date' => now()->toDateString(),
            'period_no' => 1,
            'materi' => 'Materi milik guru lain',
            'status' => 'terisi',
            'recorded_by' => $this->guruLain->id,
        ]);

        $response = $this->actingAs($this->guru)->put(route('guru.jurnal.update', [$this->assignmentLain, $journal]), $this->payload());

        $response->assertForbidden();
    }

    public function test_admin_can_monitor_journals(): void
    {
        $this->actingAs($this->guru)->post(route('guru.jurnal.store', $this->assignment), $this->payload());

        $admin = User::factory()->create(['role' => 'wakamad_kurikulum']);

        $response = $this->actingAs($admin)->get(route('jurnal.admin.index'));

        $response->assertOk();
        $response->assertSee('Membilang bilangan 1 sampai 20');
        $response->assertSee('Matematika');
    }

    public function test_guru_cannot_access_admin_monitor(): void
    {
        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.index'));

        $response->assertForbidden();
    }

    public function test_orang_tua_cannot_access_guru_journal(): void
    {
        $ortu = User::factory()->create(['role' => 'orang_tua']);

        $response = $this->actingAs($ortu)->get(route('guru.jurnal.index'));

        $response->assertForbidden();
    }
}
