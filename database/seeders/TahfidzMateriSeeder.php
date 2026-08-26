<?php

namespace Database\Seeders;

use App\Models\PembiasaanMateri;
use App\Models\PembiasaanMateriPeriode;
use Illuminate\Database\Seeder;

class TahfidzMateriSeeder extends Seeder
{
    protected array $pairs = [
        [1, 1], [1, 2], [2, 1], [2, 2], [3, 1], [3, 2],
        [4, 1], [4, 2], [5, 1], [5, 2], [6, 1], [6, 2],
    ];

    // no_urut => [nama, jenis, daftar pasangan [kelas,semester] yang aktif]
    protected array $tahfidz = [
        1 => ['Al-Fatihah', 'surah', 'all'],
        2 => ['An-Nas', 'surah', 'all'],
        3 => ['Al-Falaq', 'surah', 'all'],
        4 => ['Al-Ikhlas', 'surah', 'all'],
        5 => ['Al-Lahab', 'surah', 'all'],
        6 => ['An-Nashr', 'surah', 'all'],
        7 => ['Al-Kafirun', 'surah', 'all'],
        8 => ['Al-Kausar', 'surah', 'all'],
        9 => ['Al-Asr', 'surah', 'all'],
        10 => ['Al-Quraisy', 'surah', 'all'],
        11 => ['Al-Fiil', 'surah', [[1, 1], [1, 2], [6, 1], [6, 2]]],
        12 => ['Al-Humazah', 'surah', [[1, 1], [1, 2], [6, 1], [6, 2]]],
        13 => ['Al-Ma\'un', 'surah', [[1, 1], [1, 2], [6, 1], [6, 2]]],
        14 => ['At-Takasur', 'surah', [[1, 2], [6, 1], [6, 2]]],
        15 => ['Al-Qari\'ah', 'surah', [[1, 2], [6, 1], [6, 2]]],
        16 => ['Al-Zalzalah', 'surah', [[2, 1], [2, 2], [6, 1], [6, 2]]],
        17 => ['Al-\'Adiyat', 'surah', [[2, 1], [2, 2], [6, 1], [6, 2]]],
        18 => ['At-Tiin', 'surah', [[2, 1], [2, 2], [6, 1], [6, 2]]],
        19 => ['Al-Qadr', 'surah', [[2, 2], [6, 1], [6, 2]]],
        20 => ['Al-\'Alaq', 'surah', [[2, 2], [6, 1], [6, 2]]],
        21 => ['Al-Bayyinah', 'surah', [[3, 1], [3, 2], [6, 1], [6, 2]]],
        22 => ['Al-Insyirah', 'surah', [[3, 1], [3, 2], [6, 1], [6, 2]]],
        23 => ['Ad-Dhuha', 'surah', [[3, 1], [3, 2], [6, 1], [6, 2]]],
        24 => ['Al-Lail', 'surah', [[3, 2], [6, 1], [6, 2]]],
        25 => ['Asy-Syams', 'surah', [[3, 2], [6, 1], [6, 2]]],
        26 => ['Al-Balad', 'surah', [[4, 1], [4, 2], [6, 1], [6, 2]]],
        27 => ['Al-Fajr', 'surah', [[4, 1], [4, 2], [6, 1], [6, 2]]],
        28 => ['Al-A\'la', 'surah', [[4, 2], [6, 1], [6, 2]]],
        29 => ['Al-Ghasyiyah', 'surah', [[4, 2], [6, 1], [6, 2]]],
        30 => ['At-Thariq', 'surah', [[5, 1], [5, 2], [6, 1], [6, 2]]],
        31 => ['Al-Buruj', 'surah', [[5, 1], [5, 2], [6, 1], [6, 2]]],
        32 => ['Al-Insyiqaq', 'surah', [[5, 1], [5, 2], [6, 1], [6, 2]]],
        33 => ['Al-Mutaffifin', 'surah', [[5, 2], [6, 1], [6, 2]]],
        34 => ['Al-Infitar', 'surah', [[5, 2], [6, 1], [6, 2]]],
        35 => ['At-Takwir', 'surah', [[6, 1], [6, 2]]],
        36 => ['\'Abasa', 'surah', [[6, 1], [6, 2]]],
        37 => ['An-Nazi\'at', 'surah', [[6, 1], [6, 2]]],
        38 => ['An-Naba\'', 'surah', [[6, 1], [6, 2]]],
        39 => ['Al-Mulk', 'surah', [[4, 1], [4, 2], [6, 1], [6, 2]]],
        40 => ['Al-Waqi\'ah', 'surah', [[6, 1], [6, 2]]],
        41 => ['Yaasin', 'surah', [[5, 1], [5, 2], [6, 1], [6, 2]]],
        42 => ['Hadits tentang Menyayangi Anak Yatim', 'hadits', [[5, 1], [6, 1], [6, 2]]],
        43 => ['Hadits tentang Taqwa', 'hadits', [[5, 2], [6, 1], [6, 2]]],
        44 => ['Hadits Ciri-ciri Orang Munafiq', 'hadits', [[5, 2], [6, 1], [6, 2]]],
        45 => ['Hadits tentang Keutamaan Memberi', 'hadits', [[6, 1], [6, 2]]],
        46 => ['Hadits tentang Amal Sholeh', 'hadits', [[6, 1], [6, 2]]],
    ];

    public function run(): void
    {
        foreach ($this->tahfidz as $no => [$nama, $jenis, $aktif]) {
            $materi = PembiasaanMateri::firstOrCreate(
                ['modul' => 'tahfidz', 'no_urut' => $no],
                ['nama_materi' => $nama, 'jenis' => $jenis]
            );

            $aktifList = $aktif === 'all' ? $this->pairs : $aktif;

            foreach ($this->pairs as $pair) {
                [$kelas, $semester] = $pair;
                $isActive = in_array($pair, $aktifList, true);
                PembiasaanMateriPeriode::updateOrCreate(
                    ['materi_id' => $materi->id, 'kelas' => $kelas, 'semester' => $semester],
                    ['aktif' => $isActive]
                );
            }
        }
    }
}
