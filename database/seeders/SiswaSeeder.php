<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();

        $kelas = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $data = [
            ['I-A', 'Aisyah Nur Azizah', 'P'],
            ['I-A', 'Bilal Ramadhan', 'L'],
            ['I-A', 'Cinta Lestari Putri', 'P'],
            ['I-A', 'Dimas Prasetyo', 'L'],
            ['I-B', 'Eka Salsabila', 'P'],
            ['I-B', 'Fathir Rahman', 'L'],
            ['I-B', 'Ghina Aulia Rahma', 'P'],
            ['II-A', 'Hafizh Akbar', 'L'],
            ['II-A', 'Intan Permatasari', 'P'],
            ['II-A', 'Jaka Setiawan', 'L'],
            ['II-B', 'Khalifah Nur Hidayah', 'P'],
            ['II-B', 'Lukman Hakim', 'L'],
            ['III-A', 'Maya Anggraini', 'P'],
            ['III-A', 'Naufal Rizky', 'L'],
            ['III-B', 'Nabila Putri', 'P'],
            ['IV-A', 'Raihan Al-Farisi', 'L'],
            ['IV-A', 'Salsabila Zahra', 'P'],
            ['IV-B', 'Taufik Hidayat', 'L'],
            ['V-A', 'Umi Kulsum', 'P'],
            ['V-A', 'Vino Pratama', 'L'],
            ['V-B', 'Wulan Dari', 'P'],
            ['VI-A', 'Yusuf Maulana', 'L'],
            ['VI-A', 'Zahra Aulia', 'P'],
            ['VI-B', 'Bintang Ramadhan', 'L'],
            ['VI-B', 'Citra Ayu', 'P'],
        ];

        foreach ($data as $i => [$className, $name, $gender]) {
            $kelasModel = $kelas->firstWhere('name', $className);
            if (! $kelasModel) {
                continue;
            }

            $person = Person::firstOrCreate(
                ['nik' => '35'.str_pad((string) (1000000000 + $i), 10, '0', STR_PAD_LEFT)],
                ['name' => $name, 'gender' => $gender, 'religion' => 'Islam']
            );

            $student = Student::firstOrCreate(
                ['nis' => '24'.str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT)],
                ['person_id' => $person->id, 'name' => $name, 'gender' => $gender]
            );

            // Pastikan person_id tertaut (termasuk untuk siswa lama yang belum punya)
            if ($student->person_id !== $person->id) {
                $student->update(['person_id' => $person->id, 'name' => $name, 'gender' => $gender]);
            }

            StudentEnrollment::firstOrCreate(
                [
                    'academic_year_id' => $tahun->id,
                    'class_group_id' => $kelasModel->id,
                    'student_id' => $student->id,
                ],
                ['status' => 'aktif']
            );
        }

        // Tautkan siswa lama (dari walking skeleton) yang belum punya person
        Student::whereNull('person_id')->get()->each(function ($student) {
            $person = Person::firstOrCreate(
                ['nik' => '35'.str_pad((string) (2000000000 + $student->id), 10, '0', STR_PAD_LEFT)],
                ['name' => $student->name, 'gender' => $student->gender, 'religion' => 'Islam']
            );

            $student->update(['person_id' => $person->id]);
        });
    }
}
