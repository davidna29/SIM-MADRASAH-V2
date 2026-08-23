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

        $mapelByCode = fn (string $code) => $mapel->firstWhere('code', $code);

        $assignments = [
            // [userIndex, classIndex, mapelCode]
            [0, 0, 'MAT'],  // guru.umar -> VII-A Matematika
            [0, 0, 'IPA'],  // guru.umar -> VII-A IPA
            [1, 0, 'BIN'],  // guru.imam -> VII-A B. Indonesia
            [1, 1, 'MAT'],  // guru.imam -> VII-B Matematika
            [2, 1, 'BING'], // guru.nurul -> VII-B B. Inggris
            [0, 2, 'IPA'],  // guru.umar -> VIII-A IPA
        ];

        foreach ($assignments as [$u, $c, $code]) {
            $subject = $mapelByCode($code);
            if (! $subject) {
                continue;
            }

            TeacherAssignment::firstOrCreate(
                [
                    'academic_year_id' => $tahun->id,
                    'class_group_id' => $kelas[$c]->id,
                    'subject_id' => $subject->id,
                ],
                ['user_id' => $guru[$u]->id]
            );
        }
    }
}
