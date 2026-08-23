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

        foreach ($subjects as $s) {
            Subject::updateOrCreate(['code' => $s['code']], $s);
        }

        $classes = [
            ['name' => 'VII-A', 'grade_level' => 'VII'],
            ['name' => 'VII-B', 'grade_level' => 'VII'],
            ['name' => 'VII-C', 'grade_level' => 'VII'],
            ['name' => 'VIII-A', 'grade_level' => 'VIII'],
            ['name' => 'VIII-B', 'grade_level' => 'VIII'],
            ['name' => 'IX-A', 'grade_level' => 'IX'],
            ['name' => 'IX-B', 'grade_level' => 'IX'],
        ];

        foreach ($classes as $c) {
            ClassGroup::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}
