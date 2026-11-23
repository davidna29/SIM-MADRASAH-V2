<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Support\Misnu\StudentImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisnuStudentImporterTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicYear $tahun;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        ClassGroup::create(['name' => 'VI-A', 'grade_level' => 'VI']);
        ClassGroup::create(['name' => 'VI-B', 'grade_level' => 'VI']);
    }

    protected function row(int $line, string $name, string $nis, string $nik, string $rombel = '6A', ?string $nisn = null): array
    {
        return [
            '_row' => $line,
            'name' => $name,
            'grade' => substr($rombel, 0, 1),
            'rombel' => $rombel,
            'nis_local' => $nis,
            'nis_emis' => '111262710006'.$nis,
            'nisn' => $nisn,
            'nik' => $nik,
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_place' => 'Palangka Raya',
            'birth_date' => '2014-01-01',
            'entry_date' => '2021-07-12',
            'father_name' => 'AYAH '.$name,
            'mother_name' => 'IBU '.$name,
        ];
    }

    public function test_shared_nik_between_two_students_gets_synthetic_nik(): void
    {
        $rows = [
            $this->row(2, 'AINUN HAYATI', '256510', '6304021910180001', '6A'),
            $this->row(3, 'AKHMAD BADALI', '256503', '6304021910180001', '6B'),
        ];

        $result = (new StudentImporter($this->tahun->id))->import($rows, ['VI-A', 'VI-B']);

        $this->assertSame(2, Student::count());
        $this->assertSame(2, Person::count());
        $this->assertSame(2, Person::distinct()->count('nik'));
        $this->assertSame(2, $result['summary']['dibuat']);

        $first = Student::where('nis', '256510')->with('person')->firstOrFail();
        $second = Student::where('nis', '256503')->with('person')->firstOrFail();

        $this->assertSame('6304021910180001', $first->person->nik);
        $this->assertNotSame($first->person->nik, $second->person->nik);
        $this->assertTrue((bool) ($second->documents['nik_sintetis'] ?? false));
    }

    public function test_import_is_idempotent_for_same_nis(): void
    {
        $rows = [$this->row(2, 'SISWA SATU', '216001', '6271010101010001', '6A', '0123456789')];

        $importer = new StudentImporter($this->tahun->id);
        $importer->import($rows, ['VI-A']);
        $second = $importer->import($rows, ['VI-A']);

        $this->assertSame(1, Student::count());
        $this->assertSame(1, Student::where('nis', '216001')->count());
        $this->assertSame(1, $second['summary']['diperbarui']);
        $this->assertSame(0, $second['summary']['dibuat']);
    }

    public function test_nine_digit_nisn_is_restored_with_leading_zero(): void
    {
        $rows = [$this->row(2, 'SISWA NISN', '216002', '6271010101010002', '6A', '146171190')];

        (new StudentImporter($this->tahun->id))->import($rows, ['VI-A']);

        $this->assertSame('0146171190', Student::where('nis', '216002')->value('nisn'));
    }

    public function test_student_outside_filter_is_skipped_without_issue_noise(): void
    {
        $rows = [
            $this->row(2, 'SISWA VI', '216003', '6271010101010003', '6A'),
            $this->row(3, 'SISWA I', '216004', '', '1A'),
        ];

        $result = (new StudentImporter($this->tahun->id))->import($rows, ['VI-A']);

        $this->assertSame(1, Student::count());
        $this->assertSame(1, $result['summary']['diproses']);
        $this->assertSame([], $result['issues']);
    }
}
