<?php

namespace Tests\Feature;

use App\Exports\PrestasiTemplateExport;
use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PrestasiImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected ClassGroup $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        foreach (['240101' => 'Aisyah', '240102' => 'Bilal'] as $nis => $name) {
            $person = Person::create(['nik' => '35'.str_pad($nis, 12, '0'), 'name' => $name, 'gender' => 'L', 'religion' => 'Islam']);
            $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => $name, 'gender' => 'L']);

            StudentEnrollment::create([
                'academic_year_id' => AcademicYear::active()->id,
                'class_group_id' => $this->kelas->id,
                'student_id' => $student->id,
                'status' => 'aktif',
            ]);
        }
    }

    protected function uploadedXlsx($export): UploadedFile
    {
        $content = Excel::raw($export, ExcelFormat::XLSX);
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx').'.xlsx';
        file_put_contents($tmp, $content);

        return new UploadedFile($tmp, 'import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function test_template_download(): void
    {
        $response = $this->actingAs($this->admin)->get(route('prestasi.template'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_valid_import_preview_and_commit(): void
    {
        $file = $this->uploadedXlsx(new PrestasiTemplateExport);

        $this->actingAs($this->admin)->post(route('prestasi.import.process'), ['file' => $file])
            ->assertRedirect(route('prestasi.import.preview'));

        $this->actingAs($this->admin)->get(route('prestasi.import.preview'))
            ->assertOk()
            ->assertSee('2 valid')
            ->assertSee('Aisyah');

        $this->actingAs($this->admin)->post(route('prestasi.import.simpan'))
            ->assertRedirect(route('prestasi.index'));

        $this->assertSame(2, Achievement::count());
        $this->assertDatabaseHas('achievements', [
            'student_id' => Student::where('nis', '240101')->first()->id,
            'nama_kegiatan' => 'Lomba Pidato Bahasa Arab',
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function test_duplicate_rows_are_flagged_and_not_inserted(): void
    {
        $first = $this->uploadedXlsx(new PrestasiTemplateExport);
        $this->actingAs($this->admin)->post(route('prestasi.import.process'), ['file' => $first]);
        $this->actingAs($this->admin)->post(route('prestasi.import.simpan'));

        $second = $this->uploadedXlsx(new PrestasiTemplateExport);
        $this->actingAs($this->admin)->post(route('prestasi.import.process'), ['file' => $second]);

        $this->actingAs($this->admin)->get(route('prestasi.import.preview'))
            ->assertOk()
            ->assertSee('Duplikat');

        $this->actingAs($this->admin)->post(route('prestasi.import.simpan'));

        $this->assertSame(2, Achievement::count());
    }

    public function test_invalid_rows_are_flagged_and_skipped(): void
    {
        $invalid = new class implements FromArray, WithHeadings
        {
            public function headings(): array
            {
                return ['NIS', 'Jenis', 'Nama Kegiatan', 'Tingkat', 'Penyelenggara', 'Tanggal', 'Peringkat', 'Pembimbing', 'Status Publikasi'];
            }

            public function array(): array
            {
                return [
                    ['999999', 'akademik', 'Kegiatan A', 'nasional', '', '2026-07-01', '', '', 'publik'],
                    ['240101', 'hoki', 'Kegiatan B', 'nasional', '', '2026-07-01', '', '', 'publik'],
                    ['240101', 'akademik', 'Kegiatan B', 'kota', '', '2026-07-01', '', '', 'publik'],
                ];
            }
        };

        $file = $this->uploadedXlsx($invalid);

        $this->actingAs($this->admin)->post(route('prestasi.import.process'), ['file' => $file]);
        $this->actingAs($this->admin)->get(route('prestasi.import.preview'))
            ->assertSee('NIS tidak ditemukan')
            ->assertSee('Jenis tidak valid')
            ->assertSee('Tingkat tidak valid');

        $this->actingAs($this->admin)->post(route('prestasi.import.simpan'));

        $this->assertSame(0, Achievement::count());
    }

    public function test_batal_clears_preview(): void
    {
        $file = $this->uploadedXlsx(new PrestasiTemplateExport);
        $this->actingAs($this->admin)->post(route('prestasi.import.process'), ['file' => $file]);
        $this->actingAs($this->admin)->post(route('prestasi.import.batal'));

        $this->actingAs($this->admin)->get(route('prestasi.import.preview'))->assertOk()->assertSee('Tidak ada data untuk diimport.');
    }
}
