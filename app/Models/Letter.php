<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number',
        'date',
        'from_to',
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'disposition_to',
        'disposition_note',
        'file_url',
        'recorded_by',
        'academic_year_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Constants
    const TYPE_MASUK = 'masuk';

    const TYPE_KELUAR = 'keluar';

    const STATUSES = ['diterima', 'diproses', 'selesai', 'arsip'];

    const PRIORITIES = ['biasa', 'penting', 'segera', 'rahasia'];

    /**
     * Relasi ke category (via string, bukan FK)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(LetterCategory::class, 'category', 'name');
    }

    /**
     * Relasi ke user yang mencatat
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Relasi ke tahun ajaran
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope: surat masuk
     */
    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_MASUK);
    }

    /**
     * Scope: surat keluar
     */
    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_KELUAR);
    }

    /**
     * Scope: filter by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: search by keyword
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('number', 'like', "%{$keyword}%")
                ->orWhere('from_to', 'like', "%{$keyword}%")
                ->orWhere('subject', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    /**
     * Cek apakah surat masuk
     */
    public function isMasuk(): bool
    {
        return $this->type === self::TYPE_MASUK;
    }

    /**
     * Cek apakah surat keluar
     */
    public function isKeluar(): bool
    {
        return $this->type === self::TYPE_KELUAR;
    }

    /**
     * Label status
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'diterima' => 'Diterima',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'arsip' => 'Arsip',
            default => $this->status,
        };
    }

    /**
     * Label prioritas
     */
    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'biasa' => 'Biasa',
            'penting' => 'Penting',
            'segera' => 'Segera',
            'rahasia' => 'Rahasia',
            default => $this->priority,
        };
    }

    /**
     * Variant badge untuk status
     */
    public function statusBadgeVariant(): string
    {
        return match ($this->status) {
            'diterima' => 'info',
            'diproses' => 'warning',
            'selesai' => 'success',
            'arsip' => 'neutral',
            default => 'neutral',
        };
    }

    /**
     * Variant badge untuk prioritas
     */
    public function priorityBadgeVariant(): string
    {
        return match ($this->priority) {
            'biasa' => 'neutral',
            'penting' => 'warning',
            'segera' => 'danger',
            'rahasia' => 'primary',
            default => 'neutral',
        };
    }

    /**
     * Generate nomor surat keluar
     */
    public static function generateNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastLetter = self::where('type', self::TYPE_KELUAR)
            ->whereYear('date', $year)
            ->latest('id')
            ->first();

        $sequence = 1;
        if ($lastLetter && $lastLetter->number) {
            // Extract sequence from last number (e.g., "001/TK/08/2026" -> 1)
            if (preg_match('/^(\d+)\//', $lastLetter->number, $matches)) {
                $sequence = (int) $matches[1] + 1;
            }
        }

        return sprintf('%03d/TK/%s/%s', $sequence, $month, $year);
    }
}
