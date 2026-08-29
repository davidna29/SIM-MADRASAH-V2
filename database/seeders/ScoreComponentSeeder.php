<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ScoreComponent;
use Illuminate\Database\Seeder;

class ScoreComponentSeeder extends Seeder
{
    /**
     * Buat komponen nilai default (Tugas/PH, PTS, PAS) untuk tahun ajaran aktif.
     * Bobot default mengikuti praktik umum madrasah; dapat diubah Wakamad Kurikulum.
     */
    public function run(): void
    {
        $tahun = AcademicYear::active();

        if (! $tahun) {
            return;
        }

        $defaults = [
            ['name' => 'Tugas / Penilaian Harian', 'weight' => 40.00, 'sort_order' => 1],
            ['name' => 'PTS (Penilaian Tengah Semester)', 'weight' => 30.00, 'sort_order' => 2],
            ['name' => 'PAS (Penilaian Akhir Semester)', 'weight' => 30.00, 'sort_order' => 3],
        ];

        foreach ($defaults as $default) {
            ScoreComponent::updateOrCreate(
                ['academic_year_id' => $tahun->id, 'name' => $default['name']],
                ['weight' => $default['weight'], 'sort_order' => $default['sort_order']]
            );
        }
    }
}
