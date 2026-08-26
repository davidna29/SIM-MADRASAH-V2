<?php

namespace Database\Seeders;

use App\Models\MediaAlbum;
use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $editor = User::where('username', 'editor.humas')->first();

        if (! $editor) {
            return;
        }

        $album = MediaAlbum::firstOrCreate(
            ['slug' => Str::slug('Dokumentasi MPLS 2026')],
            [
                'title' => 'Dokumentasi MPLS 2026',
                'kategori' => 'Kegiatan',
                'description' => 'Momen masa pengenalan lingkungan sekolah tahun ajaran 2026/2027.',
                'status' => 'publik',
                'created_by' => $editor->id,
            ]
        );

        // Foto placeholder (PNG 1x1) agar galeri demo terlihat hidup
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        foreach (['Sesi perkenalan dewan guru', 'Perkenalan tata tertib madrasah', 'Penutupan MPLS'] as $i => $caption) {
            $path = 'galeri/demo-mpls-'.($i + 1).'.png';
            Storage::disk('public')->put($path, $png);

            MediaItem::firstOrCreate(
                ['album_id' => $album->id, 'file_path' => $path],
                [
                    'tipe' => 'foto',
                    'caption' => $caption,
                    'sort_order' => $i + 1,
                ]
            );
        }

        if (! $album->cover_image) {
            $album->update(['cover_image' => $album->items()->first()?->file_path]);
        }

        // Album privat (tidak tampil di website)
        MediaAlbum::firstOrCreate(
            ['slug' => Str::slug('Arsip Internal Rapat')],
            [
                'title' => 'Arsip Internal Rapat',
                'kategori' => 'Internal',
                'description' => 'Dokumentasi internal — tidak dipublikasikan.',
                'status' => 'privat',
                'created_by' => $editor->id,
            ]
        );
    }
}
