<?php

namespace Database\Seeders;

use App\Models\PublicPage;
use Illuminate\Database\Seeder;

class PublicPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            'beranda' => 'Beranda',
            'profil' => 'Profil Madrasah',
            'sambutan' => 'Sambutan Kepala Madrasah',
            'sejarah' => 'Sejarah Madrasah',
            'visi-misi' => 'Visi dan Misi',
            'kontak' => 'Kontak & Lokasi',
            'kebijakan-privasi' => 'Kebijakan Privasi',
            'sarana-prasarana' => 'Sarana & Prasarana',
        ];

        foreach ($pages as $slug => $title) {
            PublicPage::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'status' => PublicPage::DRAFT],
            );
        }
    }
}
