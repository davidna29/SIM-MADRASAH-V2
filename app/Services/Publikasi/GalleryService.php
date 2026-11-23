<?php

namespace App\Services\Publikasi;

use App\Models\MediaAlbum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $cover, int $userId): MediaAlbum
    {
        return MediaAlbum::create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug((string) ($data['slug'] ?? $data['title'])),
            'kategori' => $data['kategori'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'privat',
            'cover_image' => $cover?->store('galeri', 'public'),
            'created_by' => $userId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(MediaAlbum $album, array $data, ?UploadedFile $cover = null): MediaAlbum
    {
        $coverPath = $album->cover_image;

        if ($cover) {
            if ($coverPath && ! $album->items()->where('file_path', $coverPath)->exists()) {
                Storage::disk('public')->delete($coverPath);
            }

            $coverPath = $cover->store('galeri', 'public');
        }

        $album->update([
            'title' => $data['title'],
            'kategori' => $data['kategori'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? $album->status,
            'cover_image' => $coverPath,
        ]);

        return $album;
    }

    public function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'album';
        $candidate = $base;
        $suffix = 2;

        while (MediaAlbum::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
