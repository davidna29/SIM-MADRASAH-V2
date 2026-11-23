<?php

namespace Tests\Feature;

use App\Exports\SiswaEmisExport;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaEmisExportTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicYear $tahun;

    protected ClassGroup $kelas;

    protected User $tataUsaha;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tataUsaha = User::factory()->create(['role' => 'tata_usaha']);
        $this->tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'VI-A', 'grade_level' => 'VI']);

        $person = Person::create([
            'nik' => '6271010101010001',
            'name' => 'SISWA CONTOH',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_place' => 'Palangka Raya',
            'birth_date' => '2014-01-01',
        ]);

        $student = Student::create([
            'person_id' => $person->id,
            'nis' => '216001',
            'nisn' => '0123456789',
            'name' => 'SISWA CONTOH',
            'gender' => 'L',
            'documents' => ['emis_nis' => '111262710006216001', 'scan_kk' => 'https://drive.example/kk'],
        ]);

        StudentEnrollment::create([
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $father = Guardian::create(['name' => 'AYAH CONTOH', 'nik' => '6271010101010002', 'job' => 'Pedagang']);
        $student->guardians()->attach($father->id, ['relation' => 'ayah']);
    }

    public function test_export_has_emis_columns_and_values(): void
    {
        $export = new SiswaEmisExport($this->tahun->id);
        $student = $export->collection()->first();
        $row = $export->map($student);

        $this->assertCount(98, $export->headings());
        $this->assertCount(98, $row);
        $this->assertSame('SISWA CONTOH', $row[0]);
        $this->assertSame('VI-A', $student->enrollments->first()->classGroup->name);
        $this->assertSame(6, $row[9]);
        $this->assertSame('6A', $row[10]);
        $this->assertSame('216001', $row[16]);
        $this->assertSame('111262710006216001', $row[15]);
        $this->assertSame('AYAH CONTOH', $row[56]);
        $this->assertSame('https://drive.example/kk', $row[1]);
    }

    public function test_export_route_is_accessible_for_tata_usaha(): void
    {
        $response = $this->actingAs($this->tataUsaha)->get(route('siswa.emis'));

        $response->assertOk();
        $this->assertStringContainsString('attachment', (string) $response->headers->get('content-disposition'));
    }

    public function test_export_can_be_filtered_by_class(): void
    {
        $other = ClassGroup::create(['name' => 'VI-B', 'grade_level' => 'VI']);
        $person = Person::create(['nik' => '6271010101010003', 'name' => 'SISWA LAIN', 'gender' => 'P']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '216002', 'name' => 'SISWA LAIN', 'gender' => 'P']);
        StudentEnrollment::create([
            'academic_year_id' => $this->tahun->id,
            'class_group_id' => $other->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $export = new SiswaEmisExport($this->tahun->id, $this->kelas->id);

        $this->assertCount(1, $export->collection());
        $this->assertSame('SISWA CONTOH', $export->collection()->first()->name);
    }

    public function test_siswa_role_cannot_export(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($siswa)->get(route('siswa.emis'))->assertForbidden();
    }
}
