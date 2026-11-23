<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AcademicCalendarEvent extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'semester' => 'Awal/Akhir Semester',
        'kegiatan' => 'Kegiatan Madrasah',
        'asesmen' => 'Asesmen/Ujian',
        'rapor' => 'Pembagian Rapor',
        'libur' => 'Libur',
        'nasional' => 'Hari Nasional',
        'keagamaan' => 'Hari Besar Keagamaan',
    ];

    /** @var array<string, string> */
    public const COLORS = [
        'semester' => 'primary',
        'kegiatan' => 'info',
        'asesmen' => 'warning',
        'rapor' => 'success',
        'libur' => 'danger',
        'nasional' => 'danger',
        'keagamaan' => 'success',
    ];

    protected $fillable = [
        'academic_year_id', 'title', 'category', 'start_date', 'end_date',
        'is_libur', 'is_efektif', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_libur' => 'boolean',
            'is_efektif' => 'boolean',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function color(): string
    {
        return self::COLORS[$this->category] ?? 'neutral';
    }

    public function endDate(): Carbon
    {
        return $this->end_date ?? $this->start_date;
    }
}
