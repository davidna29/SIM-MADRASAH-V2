<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();
        $guru = User::where('role', 'guru')->orderBy('id')->get();
        $kelas = ClassGroup::orderBy('grade_level')->orderBy('name')->get();
        $mapel = Subject::orderBy('id')->get();

        $kelasByName = fn (string $name) => $kelas->firstWhere('name', $name);
        $mapelByCode = fn (string $code) => $mapel->firstWhere('code', $code);

        $assignments = [
            // [userIndex, className, mapelCode]
            [0, 'I-A', 'MAT'],    // guru.umar -> I-A Matematika
            [0, 'I-A', 'IPA'],    // guru.umar -> I-A IPA
            [1, 'I-A', 'BIN'],    // guru.imam -> I-A B. Indonesia
            [1, 'I-B', 'MAT'],    // guru.imam -> I-B Matematika
            [2, 'I-B', 'BING'],   // guru.nurul -> I-B B. Inggris
            [0, 'II-A', 'IPA'],   // guru.umar -> II-A IPA
        ];

        foreach ($assignments as [$u, $className, $code]) {
            $kelasModel = $kelasByName($className);
            $subject = $mapelByCode($code);
            if (! $kelasModel || ! $subject) {
                continue;
            }

            TeacherAssignment::firstOrCreate(
                [
                    'academic_year_id' => $tahun->id,
                    'class_group_id' => $kelasModel->id,
                    'subject_id' => $subject->id,
                ],
                ['user_id' => $guru[$u]->id]
            );
        }
    }
}
