<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class LibraryBook extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = ['tersedia', 'tidak_aktif'];

    protected $fillable = [
        'code', 'title', 'author', 'publisher', 'year', 'category_id',
        'isbn', 'total_qty', 'available_qty', 'location', 'cover_image',
        'is_ebook', 'ebook_url', 'description', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'total_qty' => 'integer',
            'available_qty' => 'integer',
            'is_ebook' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'author', 'category_id', 'total_qty', 'available_qty', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class, 'book_id');
    }
}
