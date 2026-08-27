<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamPredicateScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_period_id',
        'predikat',
        'nilai_min',
        'nilai_max',
        'deskripsi',
        'is_tidak_lulus',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_min' => 'integer',
            'nilai_max' => 'integer',
            'is_tidak_lulus' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }

    public function matches(float $nilai): bool
    {
        return $nilai >= $this->nilai_min && $nilai <= $this->nilai_max;
    }
}
