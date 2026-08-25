<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\HomeroomAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class HomeroomSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();
        if (! $tahun) {
            return;
        }

        $guru = User::where('username', 'guru.umar')->first();
        if (! $guru) {
            return;
        }

        $kelas = ClassGroup::where('name', 'VI-A')->first();
        if (! $kelas) {
            return;
        }

        HomeroomAssignment::updateOrCreate(
            ['class_group_id' => $kelas->id, 'academic_year_id' => $tahun->id],
            [
                'user_id' => $guru->id,
                'status' => 'aktif',
                'created_by' => User::where('username', 'admin')->first()?->id,
            ]
        );
    }
}
