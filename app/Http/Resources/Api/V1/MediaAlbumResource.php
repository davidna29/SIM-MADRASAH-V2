<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaAlbumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'kategori' => $this->kategori,
            'description' => $this->description,
            'status' => $this->status,
            'cover_image_url' => $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null,
            'items_count' => $this->items_count ?? null,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'tipe' => $item->tipe,
                'title' => $item->title,
                'description' => $item->description,
                'caption' => $item->caption,
                'file_url' => $item->file_path ? Storage::disk('public')->url($item->file_path) : null,
                'video_url' => $item->video_url,
                'publication_approved_at' => $item->publication_approved_at?->toIso8601String(),
            ])->values()),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
