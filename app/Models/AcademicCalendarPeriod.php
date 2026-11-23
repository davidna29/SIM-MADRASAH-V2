<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicCalendarPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id', 'semester', 'start_date', 'end_date',
        'effective_weeks', 'effective_days', 'note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'effective_weeks' => 'integer',
            'effective_days' => 'integer',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class, 'academic_year_id', 'academic_year_id');
    }
}
