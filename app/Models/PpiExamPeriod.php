<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpiExamPeriod extends Model
{
    use HasFactory;

    public const DRAFT = 'draft';

    public const SETUP = 'setup';

    public const BERLANGSUNG = 'berlangsung';

    public const SELESAI = 'selesai';

    public const DIARSIPKAN = 'diarsipkan';

    protected $fillable = [
        'academic_year_id',
        'judul',
        'tanggal_setoran_mulai',
        'tanggal_setoran_selesai',
        'tanggal_ujian',
        'status',
        'config_locked_at',
        'bobot_p1',
        'bobot_p2',
        'bobot_p3',
        'bobot_hafalan',
        'teks_mc',
        'teks_ba',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_setoran_mulai' => 'date',
            'tanggal_setoran_selesai' => 'date',
            'tanggal_ujian' => 'date',
            'config_locked_at' => 'datetime',
            'bobot_p1' => 'integer',
            'bobot_p2' => 'integer',
            'bobot_p3' => 'integer',
            'bobot_hafalan' => 'integer',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scales(): HasMany
    {
        return $this->hasMany(PpiExamPredicateScale::class, 'exam_period_id')->orderBy('urutan');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(PpiExamAspectCategory::class, 'exam_period_id')->orderBy('urutan');
    }

    public function hafalanMateri(): HasMany
    {
        return $this->hasMany(PpiExamHafalanMateri::class, 'exam_period_id')->orderBy('urutan');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(PpiExamRoom::class, 'exam_period_id')->orderBy('id');
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(PpiExamExaminer::class, 'exam_period_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(PpiExamGroup::class, 'exam_period_id')->orderBy('id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(PpiExamParticipant::class, 'exam_period_id')->orderBy('no_urut');
    }

    public function archives(): HasMany
    {
        return $this->hasMany(PpiExamArchive::class, 'exam_period_id');
    }

    public function isLocked(): bool
    {
        return $this->config_locked_at !== null;
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public static function statusLabels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::SETUP => 'Setup',
            self::BERLANGSUNG => 'Berlangsung',
            self::SELESAI => 'Selesai',
            self::DIARSIPKAN => 'Diarsipkan',
        ];
    }

    public static function transitions(): array
    {
        return [
            self::DRAFT => [self::SETUP, self::DIARSIPKAN],
            self::SETUP => [self::BERLANGSUNG, self::DRAFT],
            self::BERLANGSUNG => [self::SELESAI, self::SETUP], // mundur hanya bila belum ada nilai
            self::SELESAI => [self::DIARSIPKAN],
            self::DIARSIPKAN => [],
        ];
    }

    public function canTransitionTo(string $target): bool
    {
        return in_array($target, self::transitions()[$this->status] ?? [], true);
    }

    public function bobotTotal(): int
    {
        return $this->bobot_p1 + $this->bobot_p2 + $this->bobot_p3 + $this->bobot_hafalan;
    }

    public function bobotValid(): bool
    {
        return $this->bobotTotal() === 100;
    }
}
