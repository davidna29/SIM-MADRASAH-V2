<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MisnuSettingSeeder extends Seeder
{
    private const LOGO_SOURCE = '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/LOGO-20261122T172226Z-1-001/LOGO/LOGO MISNU - latar putih.png';

    public function run(): void
    {
        foreach (MisnuReference::settings() as $key => $value) {
            Setting::set($key, $value);
        }

        if (is_file(self::LOGO_SOURCE)) {
            $target = storage_path('app/public/settings/logo.png');

            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0775, true);
            }

            $this->resizeLogo(self::LOGO_SOURCE, $target, 900);
            $this->resizeLogo(self::LOGO_SOURCE, storage_path('app/public/settings/favicon.png'), 512, true);

            Setting::set('madrasah_logo', 'settings/logo.png');
        }

        if (! Storage::disk('public')->exists('settings')) {
            Storage::disk('public')->makeDirectory('settings');
        }
    }

    private function resizeLogo(string $source, string $target, int $maxWidth, bool $square = false): void
    {
        if (ini_get('memory_limit') !== '-1' && (int) ini_get('memory_limit') < 512) {
            ini_set('memory_limit', '512M');
        }

        $info = @getimagesize($source);

        if (! $info) {
            copy($source, $target);

            return;
        }

        [$width, $height] = $info;
        $image = imagecreatefrompng($source);

        if (! $image) {
            copy($source, $target);

            return;
        }

        $canvasWidth = $square ? $maxWidth : min($maxWidth, $width);
        $canvasHeight = $square ? $maxWidth : (int) round($height * ($canvasWidth / $width));

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $canvasWidth, $canvasHeight, $transparent);

        if ($square) {
            // Ambil bagian lambang (kiri) dari wordmark dan pusatkan pada kanvas persegi.
            $cropWidth = min($height, $width);
            $sourceX = 0;
            imagecopyresampled($canvas, $image, 0, 0, $sourceX, 0, $canvasWidth, $canvasHeight, $cropWidth, $height);
        } else {
            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $canvasWidth, $canvasHeight, $width, $height);
        }

        imagepng($canvas, $target, 9);
        imagedestroy($canvas);
        imagedestroy($image);
    }
}
