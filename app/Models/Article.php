<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Article extends Model
{
    use HasFactory, LogsActivity;

    public const DRAFT = 'draft';

    public const DIAJUKAN = 'diajukan';

    public const REVIEW = 'review';

    public const REVISI = 'revisi';

    public const DISETUJUI = 'disetujui';

    public const DIJADWALKAN = 'dijadwalkan';

    public const PUBLISH = 'publish';

    public const ARSIP = 'arsip';

    protected $fillable = [
        'title', 'slug', 'summary', 'body', 'category', 'tags',
        'featured_image', 'status', 'author_id', 'reviewer_id',
        'scheduled_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public static function statusLabels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::DIAJUKAN => 'Diajukan',
            self::REVIEW => 'Dalam Review',
            self::REVISI => 'Perlu Revisi',
            self::DISETUJUI => 'Disetujui',
            self::DIJADWALKAN => 'Dijadwalkan',
            self::PUBLISH => 'Dipublikasikan',
            self::ARSIP => 'Diarsipkan',
        ];
    }

    public static function transitions(): array
    {
        return [
            self::DRAFT => [self::DIAJUKAN, self::ARSIP],
            self::DIAJUKAN => [self::REVIEW, self::REVISI, self::DISETUJUI],
            self::REVIEW => [self::REVISI, self::DISETUJUI],
            self::REVISI => [self::DIAJUKAN],
            self::DISETUJUI => [self::DIJADWALKAN, self::PUBLISH, self::ARSIP],
            self::DIJADWALKAN => [self::PUBLISH, self::ARSIP],
            self::PUBLISH => [self::ARSIP],
            self::ARSIP => [],
        ];
    }

    public function canTransitionTo(string $target): bool
    {
        return in_array($target, self::transitions()[$this->status] ?? [], true);
    }

    public function transitionTo(string $target, ?string $scheduledAt = null, ?int $reviewerId = null): void
    {
        abort_unless($this->canTransitionTo($target), 422, 'Transisi status tidak valid.');

        $data = ['status' => $target];

        if ($target === self::DIJADWALKAN) {
            $data['scheduled_at'] = $scheduledAt ? Carbon::parse($scheduledAt) : now()->addDay();
        }

        if ($target === self::PUBLISH) {
            $data['published_at'] = now();
            $data['scheduled_at'] = null;
        }

        if (in_array($target, [self::REVIEW, self::DISETUJUI, self::PUBLISH], true)) {
            $data['reviewer_id'] = $reviewerId;
        }

        $this->update($data);
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function excerpt(int $length = 160): string
    {
        if ($this->summary) {
            return $this->summary;
        }

        return mb_strimwidth(strip_tags($this->body), 0, $length, '…');
    }
}
