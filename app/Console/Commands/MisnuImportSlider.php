<?php

namespace App\Console\Commands;

use App\Models\MediaAlbum;
use App\Models\MediaItem;
use App\Models\User;
use App\Support\Images\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MisnuImportSlider extends Command
{
    protected $signature = 'misnu:import-slider
        {--source= : Folder foto slider (default: folder "foto slider" MIS NU)}
        {--processed= : Folder gambar yang sudah dioptimalkan; disalin tanpa re-encode}';

    protected $description = 'Membuat album Sorotan Madrasah dari folder slider, mengoptimalkan gambar, dan menyetujui item.';

    private const DEFAULT_SOURCE = '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/foto slider';

    /** @var array<string, string> */
    private const TITLES = [
        'golden haflatul' => 'Haflatul Takharrruj ke-50',
        'spanduk selamat datang' => 'Penerimaan Murid Baru',
        'maulid nabi' => 'Maulid Nabi Muhammad SAW',
        'hari santri' => 'Hari Santri Nasional',
        'sumpah pemuda' => 'Sumpah Pemuda',
        'tahun baru hijriah' => 'Tahun Baru Hijriah',
        'pahlawanku' => 'Pahlawanku Teladanku',
    ];

    public function handle(): int
    {
        $processedSource = (string) $this->option('processed');
        $source = (string) ($this->option('source') ?: self::DEFAULT_SOURCE);

        if (! is_dir($source) && $processedSource !== '' && is_dir($processedSource)) {
            $source = $processedSource;
        }

        if (! is_dir($source)) {
            $this->error("Folder tidak ditemukan: {$source}");

            return self::FAILURE;
        }

        $adminId = User::where('username', 'admin')->value('id');
        $album = MediaAlbum::updateOrCreate(
            ['slug' => 'sorotan-madrasah'],
            [
                'title' => 'Sorotan Madrasah',
                'kategori' => 'Kegiatan',
                'description' => 'Foto kegiatan dan poster resmi yang tampil pada sorotan beranda.',
                'status' => 'publik',
                'created_by' => $adminId,
            ]
        );

        $sort = (int) $album->items()->max('sort_order');
        $processed = 0;
        $skipped = 0;

        foreach (File::files($source) as $file) {
            $path = $file->getPathname();

            if (! ImageOptimizer::isImage($path)) {
                $skipped++;

                continue;
            }

            $base = Str::lower($file->getFilenameWithoutExtension());
            $title = null;

            foreach (self::TITLES as $needle => $label) {
                if (str_contains($base, $needle)) {
                    $title = $label;

                    break;
                }
            }

            $slug = Str::limit(Str::slug($file->getFilenameWithoutExtension()) ?: 'foto', 60, '');
            $relative = $processedSource !== ''
                ? 'galeri/sorotan/'.$file->getFilename()
                : 'galeri/sorotan/'.$slug.'-'.substr(md5($file->getFilename()), 0, 6).'.jpg';
            $absolute = storage_path('app/public/'.$relative);

            if (! is_file($absolute)) {
                if ($processedSource !== '') {
                    if (! is_dir(dirname($absolute))) {
                        mkdir(dirname($absolute), 0775, true);
                    }
                    if (realpath($path) !== realpath($absolute)) {
                        copy($path, $absolute);
                    }
                } else {
                    ImageOptimizer::saveFit($path, $absolute, 1920, 1440, 82);
                }
            }

            $item = MediaItem::firstOrNew(['album_id' => $album->id, 'file_path' => $relative]);

            if (! $item->exists) {
                $item->sort_order = ++$sort;
            }

            $item->fill([
                'tipe' => 'foto',
                'title' => $title,
                'caption' => $title,
                'publication_approved_at' => now(),
                'publication_approved_by' => $adminId,
            ])->save();

            $processed++;
        }

        $cover = $album->items()->whereNotNull('file_path')->orderBy('sort_order')->first();
        if ($cover && $album->cover_image !== $cover->file_path) {
            $album->update(['cover_image' => $cover->file_path]);
        }

        $this->info("Album Sorotan Madrasah: {$processed} gambar diproses, {$skipped} dilewati.");

        return self::SUCCESS;
    }
}
