<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\TeachingJournal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalMingguanGuruTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;

    protected User $guruLain;

    protected TeacherAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Umar Hakim']);
        $this->guruLain = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Imam Syafii']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $tahun = AcademicYear::active();

        $kelasIA = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $kelasIIA = ClassGroup::create(['name' => 'II-A', 'grade_level' => 'II']);
        $mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika']);
        $ipa = Subject::create(['code' => 'IPA', 'name' => 'IPA']);

        $this->assignment = TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $kelasIA->id,
            'subject_id' => $mat->id,
            'user_id' => $this->guru->id,
        ]);

        TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $kelasIIA->id,
            'subject_id' => $ipa->id,
            'user_id' => $this->guru->id,
        ]);

        TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $kelasIA->id,
            'subject_id' => $ipa->id,
            'user_id' => $this->guruLain->id,
        ]);
    }

    protected function makeJournal(TeacherAssignment $assignment, string $materi, string $status, ?int $period = 1): TeachingJournal
    {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);

        return TeachingJournal::create([
            'academic_year_id' => $assignment->academic_year_id,
            'teacher_assignment_id' => $assignment->id,
            'journal_date' => $monday->toDateString(),
            'period_no' => $period,
            'materi' => $materi,
            'status' => $status,
            'recorded_by' => $assignment->user_id,
        ]);
    }

    public function test_all_authorized_roles_can_view(): void
    {
        $roles = ['guru', 'tata_usaha', 'wakamad_kurikulum', 'kepala_madrasah', 'super_admin'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get(route('jurnal.admin.mingguan.guru'));

            $response->assertOk();
        }
    }

    public function test_orang_tua_cannot_access(): void
    {
        $ortu = User::factory()->create(['role' => 'orang_tua']);

        $this->actingAs($ortu)->get(route('jurnal.admin.mingguan.guru'))->assertForbidden();
    }

    public function test_filled_entries_are_shown_and_drafts_are_not(): void
    {
        $this->makeJournal($this->assignment, 'Materi guru sudah tercatat', 'terisi');
        $this->makeJournal($this->assignment, 'Materi guru masih draf', 'draft');

        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan.guru', ['teacher_id' => $this->guru->id]));

        $response->assertOk();
        $response->assertSee('Materi guru sudah tercatat');
        $response->assertDontSee('Materi guru masih draf');
    }

    public function test_empty_day_shows_placeholder(): void
    {
        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan.guru', ['teacher_id' => $this->guru->id]));

        $response->assertOk();
        $response->assertSee('Belum ada jurnal terisi');
        $response->assertSee('Senin');
        $response->assertSee('Sabtu');
    }

    public function test_entries_are_filtered_by_selected_teacher(): void
    {
        $this->makeJournal($this->assignment, 'Jurnal milik Bapak Umar', 'terisi');

        $assignLain = TeacherAssignment::where('user_id', $this->guruLain->id)->first();
        $this->makeJournal($assignLain, 'Jurnal milik Bapak Imam', 'terisi');

        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan.guru', ['teacher_id' => $this->guru->id]));

        $response->assertOk();
        $response->assertSee('Jurnal milik Bapak Umar');
        $response->assertDontSee('Jurnal milik Bapak Imam');
    }

    public function test_teaching_load_counts_rombel_and_mapel(): void
    {
        $response = $this->actingAs($this->guru)->get(route('jurnal.admin.mingguan.guru', ['teacher_id' => $this->guru->id]));

        $response->assertOk();
        $response->assertSee('2 rombel · 2 mapel');
    }
}
