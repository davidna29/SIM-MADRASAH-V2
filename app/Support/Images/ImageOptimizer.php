<?php

namespace App\Support\Images;

use RuntimeException;

/**
 * Utilitas GD untuk menyiapkan aset foto madrasah: resize, crop persegi,
 * pemangkasan latar putih, dan re-encode JPEG tanpa metadata.
 */
final class ImageOptimizer
{
    public static function ensureMemory(): void
    {
        if (ini_get('memory_limit') !== '-1' && (int) ini_get('memory_limit') < 512) {
            ini_set('memory_limit', '512M');
        }
    }

    /**
     * Simpan gambar ke JPEG dengan latar putih, dibatasi kotak maksimum.
     */
    public static function saveFit(string $source, string $destination, int $maxWidth, int $maxHeight, int $quality = 82): array
    {
        self::ensureMemory();
        $image = self::load($source);

        [$width, $height] = [imagesx($image), imagesy($image)];
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $resized = self::resize($image, $targetWidth, $targetHeight);
        self::saveJpeg($resized, $destination, $quality);
        imagedestroy($resized);
        imagedestroy($image);

        return ['width' => $targetWidth, 'height' => $targetHeight];
    }

    /**
     * Crop tengah menjadi persegi lalu simpan JPEG.
     */
    public static function saveSquare(string $source, string $destination, int $size = 700, int $quality = 82): array
    {
        self::ensureMemory();
        $image = self::load($source);
        $width = imagesx($image);
        $height = imagesy($image);
        $edge = min($width, $height);
        $sourceX = (int) floor(($width - $edge) / 2);
        $sourceY = (int) floor(($height - $edge) / 2);

        $canvas = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $white);
        imagecopyresampled($canvas, $image, 0, 0, $sourceX, $sourceY, $size, $size, $edge, $edge);

        self::saveJpeg($canvas, $destination, $quality);
        imagedestroy($canvas);
        imagedestroy($image);

        return ['width' => $size, 'height' => $size];
    }

    /**
     * Pangkas margin putih/transparan dengan padding, lalu simpan JPEG.
     */
    public static function saveTrimmed(string $source, string $destination, int $maxWidth, int $maxHeight, int $quality = 88, int $padding = 16): array
    {
        self::ensureMemory();
        $image = self::load($source);
        $width = imagesx($image);
        $height = imagesy($image);

        $minX = $width;
        $minY = $height;
        $maxX = 0;
        $maxY = 0;
        $found = false;

        // Sampling per 2 px cukup untuk aset hero dan menjaga waktu proses.
        for ($y = 0; $y < $height; $y += 2) {
            for ($x = 0; $x < $width; $x += 2) {
                $color = imagecolorat($image, $x, $y);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                if ($r < 244 || $g < 244 || $b < 244) {
                    $found = true;
                    $minX = min($minX, $x);
                    $minY = min($minY, $y);
                    $maxX = max($maxX, $x);
                    $maxY = max($maxY, $y);
                }
            }
        }

        if (! $found) {
            throw new RuntimeException("Tidak ada subjek non-putih pada {$source}.");
        }

        $minX = max(0, $minX - $padding);
        $minY = max(0, $minY - $padding);
        $maxX = min($width - 1, $maxX + $padding);
        $maxY = min($height - 1, $maxY + $padding);

        $cropWidth = $maxX - $minX + 1;
        $cropHeight = $maxY - $minY + 1;
        $scale = min($maxWidth / $cropWidth, $maxHeight / $cropHeight, 1);
        $targetWidth = max(1, (int) round($cropWidth * $scale));
        $targetHeight = max(1, (int) round($cropHeight * $scale));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);
        imagecopyresampled($canvas, $image, 0, 0, $minX, $minY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);

        self::saveJpeg($canvas, $destination, $quality);
        imagedestroy($canvas);
        imagedestroy($image);

        return ['width' => $targetWidth, 'height' => $targetHeight];
    }

    public static function isImage(string $path): bool
    {
        return @getimagesize($path) !== false;
    }

    private static function load(string $path): \GdImage
    {
        $contents = @file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Berkas tidak dapat dibaca: {$path}");
        }

        $image = @imagecreatefromstring($contents);

        if (! $image) {
            throw new RuntimeException("Format gambar tidak didukung: {$path}");
        }

        // Flatten transparansi ke putih agar hasil JPEG konsisten.
        $canvas = imagecreatetruecolor(imagesx($image), imagesy($image));
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, imagesx($image), imagesy($image), $white);
        imagecopy($canvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);

        return $canvas;
    }

    private static function resize(\GdImage $image, int $width, int $height): \GdImage
    {
        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $white);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        return $canvas;
    }

    private static function saveJpeg(\GdImage $image, string $destination, int $quality): void
    {
        $directory = dirname($destination);

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("Folder tujuan tidak dapat dibuat: {$directory}");
        }

        if (! imagejpeg($image, $destination, $quality)) {
            throw new RuntimeException("Gagal menyimpan gambar: {$destination}");
        }
    }
}
