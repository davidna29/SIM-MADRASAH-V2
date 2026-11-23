<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Support\Images\ImageOptimizer;
use Illuminate\Console\Command;

class MisnuImportHero extends Command
{
    protected $signature = 'misnu:import-hero
        {--kamad= : Path foto kepala madrasah}
        {--group= : Path foto guru bersama}';

    protected $description = 'Menyiapkan dua foto hero beranda (kepala madrasah & guru bersama).';

    private const DEFAULT_KAMAD = '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/foto bu kamad pramuka.png';

    private const DEFAULT_GROUP = '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/foto guru bersama.png';

    public function handle(): int
    {
        $kamad = (string) ($this->option('kamad') ?: self::DEFAULT_KAMAD);
        $group = (string) ($this->option('group') ?: self::DEFAULT_GROUP);

        foreach (['kamad' => $kamad, 'group' => $group] as $label => $path) {
            if (! is_file($path)) {
                $this->error("Berkas {$label} tidak ditemukan: {$path}");

                return self::FAILURE;
            }
        }

        $kamadPath = 'settings/beranda/kepala-madrasah.jpg';
        $groupPath = 'settings/beranda/guru-bersama.jpg';

        $kamadSize = ImageOptimizer::saveTrimmed($kamad, storage_path('app/public/'.$kamadPath), 1200, 1600, 88);
        $groupSize = ImageOptimizer::saveTrimmed($group, storage_path('app/public/'.$groupPath), 1800, 1200, 86);

        Setting::set('beranda_hero_kamad', $kamadPath);
        Setting::set('beranda_hero_group', $groupPath);

        $this->table(['Foto', 'Ukuran', 'Path'], [
            ['Kepala Madrasah', $kamadSize['width'].'×'.$kamadSize['height'], $kamadPath],
            ['Guru Bersama', $groupSize['width'].'×'.$groupSize['height'], $groupPath],
        ]);

        return self::SUCCESS;
    }
}
