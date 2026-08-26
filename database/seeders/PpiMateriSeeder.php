<?php

namespace Database\Seeders;

use App\Models\PembiasaanMateri;
use App\Models\PembiasaanMateriPeriode;
use Illuminate\Database\Seeder;

class PpiMateriSeeder extends Seeder
{
    protected array $pairs = [
        [1, 1], [1, 2], [2, 1], [2, 2], [3, 1], [3, 2],
        [4, 1], [4, 2], [5, 1], [5, 2], [6, 1], [6, 2],
    ];

    // no_urut => [nama, mulai berlaku [kelas, semester]]
    protected array $ppi = [
        1 => ['Do\'a Masuk Rumah/Ruangan', [1, 1]],
        2 => ['Do\'a Mau Tidur', [1, 1]],
        3 => ['Do\'a Bangun Tidur', [1, 1]],
        4 => ['Do\'a Masuk WC', [1, 1]],
        5 => ['Do\'a Keluar WC', [1, 1]],
        6 => ['Do\'a Bercermin', [1, 2]],
        7 => ['Do\'a Senandung Al-Qur\'an', [1, 2]],
        8 => ['Do\'a Naik Kendaraan Darat', [1, 2]],
        9 => ['Do\'a Naik Kendaraan Air/Laut', [1, 2]],
        10 => ['Do\'a Keluar Rumah', [2, 1]],
        11 => ['Do\'a Mau Belajar', [2, 1]],
        12 => ['Do\'a Masuk Masjid', [2, 1]],
        13 => ['Do\'a Keluar Masjid', [2, 1]],
        14 => ['Do\'a Untuk Kedua Orang Tua', [2, 2]],
        15 => ['Do\'a Kelancaran Berbicara', [2, 2]],
        16 => ['Do\'a Sesudah Adzan', [3, 1]],
        17 => ['Do\'a Sesudah Iqamah', [3, 1]],
        18 => ['Lafaz Niat Wudhu', [1, 1]],
        19 => ['Do\'a Sesudah Wudhu', [1, 1]],
        20 => ['Niat Tayamum', [4, 1]],
        21 => ['Lafaz Niat Shalat Fardhu', [1, 1]],
        22 => ['Do\'a Iftitah', [1, 1]],
        23 => ['Bacaan Rukuk', [1, 1]],
        24 => ['Bacaan I\'tidal', [1, 1]],
        25 => ['Bacaan Sujud', [1, 1]],
        26 => ['Bacaan Duduk diantara Dua Sujud', [1, 1]],
        27 => ['Tahyat Awal', [1, 1]],
        28 => ['Tahyat Akhir', [1, 1]],
        29 => ['Do\'a Qunut', [3, 2]],
        30 => ['Do\'a Sebelum Salam', [3, 2]],
        31 => ['Do\'a Salamat', [3, 2]],
        32 => ['Wirid Setelah Shalat', [3, 2]],
        33 => ['Niat Shalat Jenazah', [6, 1]],
        34 => ['Takbir Pertama', [6, 1]],
        35 => ['Takbir Kedua', [6, 1]],
        36 => ['Takbir Ketiga', [6, 1]],
        37 => ['Takbir Keempat', [6, 1]],
    ];

    public function run(): void
    {
        foreach ($this->ppi as $no => [$nama, $start]) {
            $materi = PembiasaanMateri::firstOrCreate(
                ['modul' => 'ppi', 'no_urut' => $no],
                ['nama_materi' => $nama, 'jenis' => null]
            );

            $startIdx = $this->idx($start);
            foreach ($this->pairs as $i => $pair) {
                [$kelas, $semester] = $pair;
                PembiasaanMateriPeriode::updateOrCreate(
                    ['materi_id' => $materi->id, 'kelas' => $kelas, 'semester' => $semester],
                    ['aktif' => $i >= $startIdx]
                );
            }
        }
    }

    protected function idx(array $pair): int
    {
        return (int) array_search($pair, $this->pairs, true);
    }
}
