<?php

namespace App\Console\Commands;

use App\Models\MediaAlbum;
use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MisnuImportGallery extends Command
{
    protected $signature = 'misnu:import-galeri
        {--source=* : Folder sumber; satu folder dipakai untuk semua album, dua folder untuk (Dokumentasi, Kegiatan)}
        {--approve : Tandai item sudah disetujui untuk tampil publik}';

    protected $description = 'Membuat album galeri MIS NU dari folder foto dan menyalin berkas ke storage publik.';

    private const ALBUMS = [
        'Dokumentasi MIS NU' => [
            'kategori' => 'Dokumentasi',
            'description' => 'Dokumentasi kegiatan dan fasilitas MI Nahdlatul Ulama Palangka Raya.',
            'sources' => [
                '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/PROFIL 2023-2024-20261122T171523Z-1-001/PROFIL 2023-2024/dokumentasi',
            ],
        ],
        'Kegiatan Madrasah' => [
            'kategori' => 'Kegiatan',
            'description' => 'Kegiatan belajar, keagamaan, dan ekstrakurikuler MI Nahdlatul Ulama Palangka Raya.',
            'sources' => [
                '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/kumpulan berita MIS NU',
            ],
        ],
    ];

    public function handle(): int
    {
        $adminId = User::where('username', 'admin')->value('id');
        $approve = (bool) $this->option('approve');
        $customSources = array_values(array_filter((array) $this->option('source')));
        $total = 0;
        $albumIndex = 0;

        foreach (self::ALBUMS as $title => $definition) {
            $sources = match (true) {
                count($customSources) === 1 => [$customSources[0]],
                count($customSources) >= 2 => [$customSources[min($albumIndex, count($customSources) - 1)]],
                default => $definition['sources'],
            };
            $albumIndex++;
            $files = collect();

            foreach ($sources as $source) {
                if (! is_dir($source)) {
                    $this->warn("Folder tidak ditemukan, dilewati: {$source}");

                    continue;
                }

                $files = $files->merge(
                    collect(File::files($source))
                        ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
                        ->reject(fn ($file) => Str::startsWith($file->getFilename(), ['Screenshot', 'WhatsApp']))
                );
            }

            $album = MediaAlbum::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'kategori' => $definition['kategori'],
                    'description' => $definition['description'],
                    'status' => 'publik',
                    'created_by' => $adminId,
                ]
            );

            $destination = storage_path('app/public/galeri/misnu/'.$album->slug);

            if (! is_dir($destination)) {
                mkdir($destination, 0775, true);
            }

            $sort = (int) $album->items()->max('sort_order');
            $added = 0;
            $cover = $album->cover_image;

            foreach ($files as $file) {
                $filename = Str::slug(pathinfo($file->getFilename(), PATHINFO_FILENAME)) ?: 'foto';
                $filename .= '.'.strtolower($file->getExtension());
                $relative = 'galeri/misnu/'.$album->slug.'/'.$filename;

                if (! is_file(storage_path('app/public/'.$relative))) {
                    copy($file->getPathname(), storage_path('app/public/'.$relative));
                }

                MediaItem::updateOrCreate(
                    ['album_id' => $album->id, 'file_path' => $relative],
                    [
                        'tipe' => 'foto',
                        'caption' => null,
                        'sort_order' => ++$sort,
                        'publication_approved_at' => $approve ? now() : null,
                        'publication_approved_by' => $approve ? $adminId : null,
                    ]
                );

                $cover ??= $relative;
                $added++;
            }

            if ($cover && $album->cover_image !== $cover) {
                $album->update(['cover_image' => $cover]);
            }

            $total += $added;
            $this->line(sprintf('%s: %d foto', $title, $added));
        }

        if (! $approve) {
            $this->warn('Item ditandai belum disetujui. Jalankan ulang dengan --approve untuk menampilkan di publik.');
        }

        $this->info("Selesai — {$total} item galeri diproses.");

        return self::SUCCESS;
    }
}
