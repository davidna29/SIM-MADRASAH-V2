<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PublicPage extends Model
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'publik';

    protected $fillable = [
        'slug', 'title', 'subtitle', 'summary', 'body', 'featured_image',
        'image_alt', 'status', 'meta_description', 'created_by',
    ];

    protected $attributes = [
        'status' => self::DRAFT,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::PUBLISHED);
    }
}
