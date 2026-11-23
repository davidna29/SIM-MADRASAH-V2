<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'category' => $this->category,
            'tags' => $this->tags ? array_values(array_filter(array_map('trim', explode(',', $this->tags)))) : [],
            'status' => $this->status,
            'author' => $this->author?->name,
            'featured_image_url' => $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null,
            'published_at' => $this->published_at?->toIso8601String(),
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'excerpt' => $this->excerpt(200),
            'url' => route('publik.berita.show', $this->slug),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
