<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\CounselingSession;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KonselingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $guruBk;

    protected User $otherGuruBk;

    protected User $kepala;

    protected User $waliKelas;

    protected StudentEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->guruBk = User::factory()->create(['role' => 'guru_bk', 'name' => 'Guru BK A']);
        $this->otherGuruBk = User::factory()->create(['role' => 'guru_bk', 'name' => 'Guru BK B']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->waliKelas = User::factory()->create(['role' => 'wali_kelas']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $person = Person::create(['nik' => '3525100101', 'name' => 'Aisyah', 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '251001', 'name' => 'Aisyah', 'gender' => 'P']);
        $this->enrollment = StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'student_enrollment_id' => $this->enrollment->id,
            'session_date' => now()->format('Y-m-d'),
            'counseling_type' => 'individual',
            'topic' => 'Motivasi Belajar',
            'problem_description' => 'Siswa menunjukkan penurunan motivasi.',
            'assessment_result' => 'Perlu pendekatan khusus.',
            'action_taken' => 'Sesi konseling dilakukan.',
            'follow_up_plan' => 'Pemantauan mingguan.',
            'confidentiality_level' => 'plus_wali_kelas',
        ], $overrides);
    }

    public function test_guru_bk_can_create_counseling_session(): void
    {
        $response = $this->actingAs($this->guruBk)->post(route('konseling.store'), $this->payload());

        $response->assertRedirect();
        $this->assertDatabaseHas('counseling_sessions', [
            'counselor_user_id' => $this->guruBk->id,
            'topic' => 'Motivasi Belajar',
        ]);
    }

    public function test_admin_can_create_counseling_session(): void
    {
        $response = $this->actingAs($this->admin)->post(route('konseling.store'), $this->payload());

        $response->assertRedirect();
        $this->assertDatabaseHas('counseling_sessions', [
            'counselor_user_id' => $this->admin->id,
        ]);
    }

    public function test_guru_bk_can_view_own_sessions(): void
    {
        CounselingSession::create([
            ...$this->payload(),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->guruBk)->get(route('konseling.index'));

        $response->assertOk();
        $response->assertSee('Motivasi Belajar');
    }

    public function test_guru_bk_can_view_other_counselor_sessions(): void
    {
        CounselingSession::create([
            ...$this->payload(['confidentiality_level' => 'guru_bk_only']),
            'counselor_user_id' => $this->otherGuruBk->id,
        ]);

        $response = $this->actingAs($this->guruBk)->get(route('konseling.index'));

        $response->assertOk();
        $response->assertSee('Motivasi Belajar');
    }

    public function test_kepala_can_view_plus_kepala_sessions(): void
    {
        CounselingSession::create([
            ...$this->payload(['confidentiality_level' => 'plus_kepala']),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->kepala)->get(route('konseling.index'));

        $response->assertOk();
        $response->assertSee('Motivasi Belajar');
    }

    public function test_kepala_cannot_view_guru_bk_only_sessions(): void
    {
        CounselingSession::create([
            ...$this->payload(['confidentiality_level' => 'guru_bk_only']),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->kepala)->get(route('konseling.index'));

        $response->assertOk();
        $response->assertDontSee('Motivasi Belajar');
    }

    public function test_wali_kelas_can_view_plus_wali_kelas_sessions(): void
    {
        CounselingSession::create([
            ...$this->payload(['confidentiality_level' => 'plus_wali_kelas']),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->waliKelas)->get(route('konseling.index'));

        $response->assertOk();
        $response->assertSee('Motivasi Belajar');
    }

    public function test_wali_kelas_cannot_view_plus_kepala_sessions(): void
    {
        CounselingSession::create([
            ...$this->payload(['confidentiality_level' => 'plus_kepala']),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->waliKelas)->get(route('konseling.index'));

        $response->assertOk();
        $response->assertDontSee('Motivasi Belajar');
    }

    public function test_guru_bk_can_update_own_session(): void
    {
        $session = CounselingSession::create([
            ...$this->payload(),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->guruBk)->put(route('konseling.update', $session), $this->payload([
            'topic' => 'Topik Baru',
            'status' => 'ditutup',
        ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('counseling_sessions', ['id' => $session->id, 'topic' => 'Topik Baru', 'status' => 'ditutup']);
    }

    public function test_admin_can_delete_session(): void
    {
        $session = CounselingSession::create([
            ...$this->payload(),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $this->actingAs($this->admin)->delete(route('konseling.destroy', $session));

        $this->assertDatabaseMissing('counseling_sessions', ['id' => $session->id]);
    }

    public function test_guru_bk_cannot_delete_session(): void
    {
        $session = CounselingSession::create([
            ...$this->payload(),
            'counselor_user_id' => $this->guruBk->id,
        ]);

        $response = $this->actingAs($this->guruBk)->delete(route('konseling.destroy', $session));

        $response->assertForbidden();
    }

    public function test_guru_cannot_access_counseling_module(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('konseling.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('konseling.create'))->assertForbidden();
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('konseling.index'))->assertRedirect(route('login'));
    }

    public function test_confidentiality_level_is_required(): void
    {
        $response = $this->actingAs($this->guruBk)->post(route('konseling.store'), $this->payload([
            'confidentiality_level' => '',
        ]));

        $response->assertSessionHasErrors('confidentiality_level');
    }
}
