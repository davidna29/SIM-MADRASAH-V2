<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Report;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RaporMultiMapelTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUmar;

    protected User $guruImam;

    protected ClassGroup $kelas;

    protected Subject $mat;

    protected Subject $ipa;

    protected TeacherAssignment $assignMat;

    protected TeacherAssignment $assignIpa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guruUmar = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Umar Hakim']);
        $this->guruImam = User::factory()->create(['role' => 'guru', 'name' => 'Bapak Imam Syafii']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $tahun = AcademicYear::active();

        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        $this->mat = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'sort_order' => 1]);
        $this->ipa = Subject::create(['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'sort_order' => 2]);

        $this->assignMat = TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $this->kelas->id,
            'subject_id' => $this->mat->id,
            'user_id' => $this->guruUmar->id,
        ]);

        $this->assignIpa = TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $this->kelas->id,
            'subject_id' => $this->ipa->id,
            'user_id' => $this->guruImam->id,
        ]);
    }

    protected function makeStudent(string $nis, string $name): StudentEnrollment
    {
        $person = Person::create(['nik' => '35'.str_pad($nis, 12, '0'), 'name' => $name, 'gender' => 'L', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => $name, 'gender' => 'L']);

        return StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function score(StudentEnrollment $enrollment, Subject $subject, int $value): void
    {
        Score::updateOrCreate(
            [
                'student_enrollment_id' => $enrollment->id,
                'subject_id' => $subject->id,
                'academic_year_id' => AcademicYear::active()->id,
                'semester' => AcademicYear::active()->semester,
            ],
            ['score' => $value]
        );
    }

    public function test_publishing_two_subjects_produces_single_report_with_two_items(): void
    {
        $enrollment = $this->makeStudent('251001', 'Aisyah');
        $this->score($enrollment, $this->mat, 85);
        $this->score($enrollment, $this->ipa, 78);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat))->assertRedirect(route('guru.penugasan'));
        $this->actingAs($this->guruImam)->post(route('guru.nilai.terbitkan', $this->assignIpa))->assertRedirect(route('guru.penugasan'));

        $tahun = AcademicYear::active();

        $this->assertSame(1, Report::where('student_id', $enrollment->student_id)
            ->where('academic_year_id', $tahun->id)
            ->where('semester', $tahun->semester)
            ->count());

        $report = Report::where('student_id', $enrollment->student_id)->first();
        $this->assertSame(2, $report->items()->count());
        $this->assertEqualsCanonicalizing(['MAT', 'IPA'], $report->items()->pluck('subject_code')->all());
    }

    public function test_republishing_is_idempotent(): void
    {
        $enrollment = $this->makeStudent('251002', 'Bilal');
        $this->score($enrollment, $this->mat, 85);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));

        $this->score($enrollment, $this->mat, 90);
        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));

        $tahun = AcademicYear::active();

        $this->assertSame(1, Report::where('student_id', $enrollment->student_id)->count());
        $this->assertSame(1, Report::first()->items()->count());
        $this->assertSame(90, Report::first()->items()->first()->score);
    }

    public function test_student_without_score_is_skipped(): void
    {
        $this->makeStudent('251003', 'Citra');
        $enrollment = $this->makeStudent('251004', 'Dimas');
        $this->score($enrollment, $this->mat, 80);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));

        $this->assertSame(1, Report::count());
        $this->assertSame(1, Report::first()->items()->count());
    }

    public function test_guru_penugasan_list_shows_rapor_with_their_subject(): void
    {
        $enrollment = $this->makeStudent('251005', 'Eka');
        $this->score($enrollment, $this->mat, 85);
        $this->score($enrollment, $this->ipa, 78);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));
        $this->actingAs($this->guruImam)->post(route('guru.nilai.terbitkan', $this->assignIpa));

        $this->actingAs($this->guruUmar)->get(route('guru.penugasan'))->assertSee('Eka');
        $this->actingAs($this->guruImam)->get(route('guru.penugasan'))->assertSee('Eka');
    }

    public function test_teacher_of_other_subject_cannot_open_or_download_rapor(): void
    {
        $enrollment = $this->makeStudent('251006', 'Fajar');
        $this->score($enrollment, $this->mat, 85);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));
        $report = Report::first();

        $this->actingAs($this->guruUmar)->get(route('guru.rapor', $report))->assertOk();
        $this->actingAs($this->guruImam)->get(route('guru.rapor', $report))->assertForbidden();
        $this->actingAs($this->guruImam)->get(route('guru.rapor.unduh', $report))->assertForbidden();
    }

    public function test_guru_rapor_view_shows_all_subjects(): void
    {
        $enrollment = $this->makeStudent('251007', 'Gita');
        $this->score($enrollment, $this->mat, 85);
        $this->score($enrollment, $this->ipa, 78);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));
        $this->actingAs($this->guruImam)->post(route('guru.nilai.terbitkan', $this->assignIpa));

        $response = $this->actingAs($this->guruUmar)->get(route('guru.rapor', Report::first()));

        $response->assertOk();
        $response->assertSee('Matematika');
        $response->assertSee('Ilmu Pengetahuan Alam');
        $response->assertSee('85');
        $response->assertSee('78');
    }

    public function test_ortu_rapor_view_shows_all_subjects(): void
    {
        $enrollment = $this->makeStudent('251008', 'Hasan');
        $this->score($enrollment, $this->mat, 85);
        $this->score($enrollment, $this->ipa, 78);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));
        $this->actingAs($this->guruImam)->post(route('guru.nilai.terbitkan', $this->assignIpa));

        $ibu = User::factory()->create(['role' => 'orang_tua']);
        $guardian = Guardian::create(['user_id' => $ibu->id, 'name' => 'Ibu Hasan']);
        $guardian->students()->attach($enrollment->student_id);

        $response = $this->actingAs($ibu)->get(route('ortu.rapor', $enrollment->student_id));

        $response->assertOk();
        $response->assertSee('Matematika');
        $response->assertSee('Ilmu Pengetahuan Alam');
    }

    public function test_rapor_pdf_download(): void
    {
        $enrollment = $this->makeStudent('251009', 'Ilham');
        $this->score($enrollment, $this->mat, 85);

        $this->actingAs($this->guruUmar)->post(route('guru.nilai.terbitkan', $this->assignMat));

        $response = $this->actingAs($this->guruUmar)->get(route('guru.rapor.unduh', Report::first()));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_legacy_reports_are_backfilled_and_consolidated(): void
    {
        $enrollment = $this->makeStudent('251010', 'Joko');
        $tahun = AcademicYear::active();
        $student = $enrollment->student;

        // Simulasikan environment lama sebelum migrasi 000013: unique belum ada,
        // sehingga rapor terfragmentasi (1 baris per mapel) bisa tersimpan.
        // Di MySQL index unique dipakai FK student_id, jadi FK dilepas dulu (sama seperti 000003).
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropUnique('report_unique');
        });

        $legacy = function (string $mapel, string $kode, string $guru, int $score) use ($tahun, $student) {
            return Report::create([
                'student_id' => $student->id,
                'academic_year_id' => $tahun->id,
                'semester' => $tahun->semester,
                'version' => 1,
                'status' => 'terbit',
                'snapshot' => [
                    'tahun' => $tahun->name,
                    'semester' => $tahun->semester,
                    'kelas' => 'I-A',
                    'mapel' => $mapel,
                    'kode_mapel' => $kode,
                    'guru' => $guru,
                    'nis' => $student->nis,
                    'siswa' => $student->name,
                    'score' => $score,
                    'terbit_pada' => now()->toDateTimeString(),
                ],
            ]);
        };

        $legacy('Matematika', 'MAT', 'Bapak Umar', 85);
        $legacy('Ilmu Pengetahuan Alam', 'IPA', 'Bapak Imam', 78);

        $this->assertSame(2, Report::where('student_id', $student->id)->count());

        $migration = require base_path('database/migrations/2026_08_24_000013_consolidate_legacy_reports.php');
        $migration->up();

        $this->assertSame(1, Report::where('student_id', $student->id)->count());

        $report = Report::where('student_id', $student->id)->first();
        $this->assertSame(2, $report->items()->count());
        $this->assertEqualsCanonicalizing(['MAT', 'IPA'], $report->items()->pluck('subject_code')->all());
        $this->assertSame(85, $report->items()->where('subject_code', 'MAT')->first()->score);
    }
}
