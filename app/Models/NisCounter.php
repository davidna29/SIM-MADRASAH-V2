<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NisCounter extends Model
{
    protected $fillable = ['academic_year_id', 'last_number'];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get next NIS number and increment counter.
     */
    public static function nextNumber(int $academicYearId): int
    {
        $counter = static::firstOrCreate(
            ['academic_year_id' => $academicYearId],
            ['last_number' => 0]
        );

        $counter->increment('last_number');

        return $counter->last_number;
    }

    /**
     * Preview next number without incrementing.
     */
    public static function peekNext(int $academicYearId): int
    {
        $counter = static::firstOrCreate(
            ['academic_year_id' => $academicYearId],
            ['last_number' => 0]
        );

        return $counter->last_number + 1;
    }
}
