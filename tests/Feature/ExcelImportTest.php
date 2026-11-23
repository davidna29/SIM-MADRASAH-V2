<?php

namespace Tests\Feature;

use App\Exports\SimpleTemplateExport;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $tataUsaha;

    protected User $wakamadKurikulum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['username' => 'admin', 'role' => 'super_admin']);
        $this->tataUsaha = User::factory()->create(['role' => 'tata_usaha']);
        $this->wakamadKurikulum = User::factory()->create(['role' => 'wakamad_kurikulum']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        ClassGroup::create(['name' => 'VI-A', 'grade_level' => 'VI']);
        Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
    }

    /**
     * @param  list<string>  $headings
     * @param  list<array<int, mixed>>  $rows
     */
    protected function workbook(array $headings, array $rows, string $filename): UploadedFile
    {
        $content = Excel::raw(new SimpleTemplateExport($headings, $rows), ExcelFormat::XLSX);

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_student_import_preview_and_commit(): void
    {
        $file = $this->workbook(
            ['nis', 'nisn', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'nik', 'kelas', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_hp'],
            [['216100', '0123456780', 'SISWA IMPORT', 'L', 'Palangka Raya', '2014-05-01', '6271010101010099', 'VI-A', 'AYAH IMPORT', 'IBU IMPORT', '', '081200000000']],
            'siswa.xlsx'
        );

        $this->actingAs($this->tataUsaha)
            ->post(route('siswa.import.process'), ['file' => $file])
            ->assertRedirect(route('siswa.import.preview'));

        $this->assertIsArray(session('excel_import_siswa'));
        $this->assertNull(session('excel_import_siswa')[0]['error']);

        $this->actingAs($this->tataUsaha)
            ->post(route('siswa.import.simpan'))
            ->assertRedirect(route('siswa.index'));

        $this->assertDatabaseHas('students', ['nis' => '216100', 'name' => 'SISWA IMPORT']);
        $this->assertDatabaseHas('student_enrollments', ['status' => 'aktif']);
        $this->assertDatabaseHas('guardians', ['name' => 'AYAH IMPORT']);
    }

    public function test_student_import_rejects_unknown_class(): void
    {
        $file = $this->workbook(
            ['nis', 'nama', 'jenis_kelamin', 'kelas'],
            [['216101', 'SISWA KELAS SALAH', 'P', 'IX-Z']],
            'siswa.xlsx'
        );

        $this->actingAs($this->tataUsaha)->post(route('siswa.import.process'), ['file' => $file]);

        $row = session('excel_import_siswa')[0];
        $this->assertNotNull($row['error']);
        $this->assertStringContainsString('Kelas tidak ditemukan', $row['error']);
    }

    public function test_employee_import_creates_employee(): void
    {
        $file = $this->workbook(
            ['nip', 'nuptk', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'status_pegawai', 'jabatan', 'unit_kerja', 'tmt', 'jenjang_pendidikan', 'jurusan'],
            [['198001012005011001', '', 'GURU IMPORT', '6271010101010088', 'P', 'Palangka Raya', '1980-01-01', 'pns', 'GURU_MAPEL', 'GURU', '2005-01-01', 'S1', 'Tarbiyah']],
            'guru.xlsx'
        );

        $this->actingAs($this->tataUsaha)->post(route('guru.import.process'), ['file' => $file])->assertRedirect(route('guru.import.preview'));
        $this->actingAs($this->tataUsaha)->post(route('guru.import.simpan'))->assertRedirect(route('pegawai.index'));

        $person = Person::where('name', 'GURU IMPORT')->first();
        $this->assertNotNull($person);
        $this->assertDatabaseHas('employees', ['person_id' => $person->id, 'nip' => '198001012005011001', 'employee_status' => 'pns']);
    }

    public function test_subject_import_creates_subject_with_jp(): void
    {
        $file = $this->workbook(
            ['kode', 'nama', 'kelompok', 'jp_I', 'jp_II', 'jp_III', 'jp_IV', 'jp_V', 'jp_VI'],
            [['QUR', "Al-Qur'an Hadis", 'keagamaan', 2, 2, 2, 2, 2, 2]],
            'mapel.xlsx'
        );

        $this->actingAs($this->wakamadKurikulum)->post(route('mapel.import.process'), ['file' => $file])->assertRedirect(route('mapel.import.preview'));
        $this->actingAs($this->wakamadKurikulum)->post(route('mapel.import.simpan'))->assertRedirect(route('mapel.index'));

        $subject = Subject::where('code', 'QUR')->first();
        $this->assertNotNull($subject);
        $this->assertSame(2, $subject->jp_per_grade['VI']);
    }

    public function test_duplicate_subject_code_is_rejected_in_preview(): void
    {
        Subject::create(['code' => 'MAT', 'name' => 'Matematika']);

        $file = $this->workbook(
            ['kode', 'nama', 'kelompok'],
            [['MAT', 'Matematika Baru', 'umum']],
            'mapel.xlsx'
        );

        $this->actingAs($this->wakamadKurikulum)->post(route('mapel.import.process'), ['file' => $file]);

        $this->assertStringContainsString('sudah ada', session('excel_import_mapel')[0]['error']);
    }

    public function test_templates_can_be_downloaded(): void
    {
        $this->actingAs($this->tataUsaha)->get(route('siswa.import.template'))->assertOk();
        $this->actingAs($this->tataUsaha)->get(route('guru.import.template'))->assertOk();
        $this->actingAs($this->wakamadKurikulum)->get(route('mapel.import.template'))->assertOk();
    }

    public function test_import_pages_are_role_protected(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('siswa.import'))->assertForbidden();
        $this->actingAs($guru)->get(route('guru.import'))->assertForbidden();
        $this->actingAs($guru)->get(route('mapel.import'))->assertForbidden();
    }

    public function test_cancel_clears_session(): void
    {
        $this->actingAs($this->tataUsaha)->post(route('siswa.import.process'), [
            'file' => $this->workbook(['nis', 'nama', 'jenis_kelamin', 'kelas'], [['216102', 'X', 'L', 'VI-A']], 'siswa.xlsx'),
        ]);

        $this->assertNotNull(session('excel_import_siswa'));

        $this->actingAs($this->tataUsaha)->post(route('siswa.import.batal'));

        $this->assertNull(session('excel_import_siswa'));
    }
}
