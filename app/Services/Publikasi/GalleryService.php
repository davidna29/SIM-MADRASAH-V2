<?php

namespace App\Services\Publikasi;

use App\Models\MediaAlbum;
use App\Models\MediaItem;
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

    /**
     * @param  array<string, mixed>  $data
     */
    public function addItem(MediaAlbum $album, array $data, ?UploadedFile $file): MediaItem
    {
        return $album->items()->create([
            'tipe' => $data['tipe'],
            'title' => $data['title'],
            'description' => $data['deskripsi'] ?? null,
            'file_path' => $file?->store('galeri', 'public'),
            'video_url' => $data['video_url'] ?? null,
            'caption' => $data['title'],
            'sort_order' => (int) $album->items()->max('sort_order') + 1,
            'publication_approved_at' => null,
            'publication_approved_by' => null,
        ]);
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
