<?php

namespace App\Services\Publikasi;

use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $image, int $authorId): Article
    {
        $slug = $this->uniqueSlug((string) (($data['slug'] ?? '') ?: Str::slug($data['title'])));

        return Article::create([
            'title' => $data['title'],
            'slug' => $slug,
            'summary' => $data['summary'] ?? null,
            'body' => $data['body'],
            'category' => $data['category'] ?? null,
            'tags' => $data['tags'] ?? null,
            'featured_image' => $image?->store('berita', 'public'),
            'status' => Article::DRAFT,
            'author_id' => $authorId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Article $article, array $data, ?UploadedFile $image = null): Article
    {
        $featured = $article->featured_image;

        if ($image) {
            if ($featured) {
                Storage::disk('public')->delete($featured);
            }

            $featured = $image->store('berita', 'public');
        }

        $slug = $article->slug;
        if (! empty($data['slug'])) {
            $slug = $this->uniqueSlug((string) $data['slug'], $article->id);
        }

        $article->update([
            'title' => $data['title'],
            'slug' => $slug,
            'summary' => $data['summary'] ?? null,
            'body' => $data['body'],
            'category' => $data['category'] ?? null,
            'tags' => $data['tags'] ?? null,
            'featured_image' => $featured,
        ]);

        return $article;
    }

    public function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'berita';
        $candidate = $base;
        $suffix = 2;

        while (Article::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
