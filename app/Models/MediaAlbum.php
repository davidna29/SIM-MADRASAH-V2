<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class MediaAlbum extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title', 'slug', 'kategori', 'description', 'cover_image', 'status', 'created_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function items(): HasMany
    {
        return $this->hasMany(MediaItem::class, 'album_id')->orderBy('sort_order')->orderBy('id');
    }

    public function photos(): HasMany
    {
        return $this->items()->where('tipe', 'foto');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
