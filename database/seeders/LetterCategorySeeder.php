<?php

namespace Database\Seeders;

use App\Models\LetterCategory;
use Illuminate\Database\Seeder;

class LetterCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Undangan', 'description' => 'Surat undangan acara/meeting', 'sort_order' => 1],
            ['name' => 'Pemberitahuan', 'description' => 'Surat pemberitahuan resmi', 'sort_order' => 2],
            ['name' => 'Edaran', 'description' => 'Surat edaran ke seluruh unit', 'sort_order' => 3],
            ['name' => 'Nota Dinas', 'description' => 'Nota dinas internal', 'sort_order' => 4],
            ['name' => 'Surat Tugas', 'description' => 'Penugasan pegawai/guru', 'sort_order' => 5],
            ['name' => 'Surat Keterangan', 'description' => 'Surat keterangan resmi', 'sort_order' => 6],
            ['name' => 'Laporan', 'description' => 'Surat laporan kegiatan', 'sort_order' => 7],
            ['name' => 'Permohonan', 'description' => 'Surat permohonan', 'sort_order' => 8],
            ['name' => 'Lainnya', 'description' => 'Kategori lainnya', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            LetterCategory::create($category);
        }
    }
}
