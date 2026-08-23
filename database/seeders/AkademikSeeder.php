<?php

namespace Database\Seeders;

use App\Models\ClassGroup;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class AkademikSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'MAT', 'name' => 'Matematika'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia'],
            ['code' => 'BING', 'name' => 'Bahasa Inggris'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam'],
            ['code' => 'SBK', 'name' => 'Seni Budaya dan Keterampilan'],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['code' => 'PKN', 'name' => 'Pendidikan Kewarganegaraan'],
            ['code' => 'BTA', 'name' => 'Baca Tulis Al-Qur\'an'],
        ];

        foreach ($subjects as $i => $s) {
            Subject::updateOrCreate(
                ['code' => $s['code']],
                ['name' => $s['name'], 'sort_order' => $i + 1]
            );
        }

        $classes = [
            ['name' => 'I-A', 'grade_level' => 'I'],
            ['name' => 'I-B', 'grade_level' => 'I'],
            ['name' => 'I-C', 'grade_level' => 'I'],
            ['name' => 'II-A', 'grade_level' => 'II'],
            ['name' => 'II-B', 'grade_level' => 'II'],
            ['name' => 'III-A', 'grade_level' => 'III'],
            ['name' => 'III-B', 'grade_level' => 'III'],
            ['name' => 'IV-A', 'grade_level' => 'IV'],
            ['name' => 'IV-B', 'grade_level' => 'IV'],
            ['name' => 'V-A', 'grade_level' => 'V'],
            ['name' => 'V-B', 'grade_level' => 'V'],
            ['name' => 'VI-A', 'grade_level' => 'VI'],
            ['name' => 'VI-B', 'grade_level' => 'VI'],
        ];

        foreach ($classes as $c) {
            ClassGroup::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}
