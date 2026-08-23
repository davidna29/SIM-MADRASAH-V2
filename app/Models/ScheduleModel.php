<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ScheduleModel extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'schedule_models';

    protected $fillable = [
        'academic_year_id', 'name', 'start_time', 'max_hours_per_day', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'max_hours_per_day' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'start_time', 'max_hours_per_day', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function gradeLevelRows(): HasMany
    {
        return $this->hasMany(ScheduleModelGradeLevel::class);
    }

    public function gradeLevels(): array
    {
        return $this->gradeLevelRows()->pluck('grade_level')->all();
    }

    public function slots(): HasMany
    {
        return $this->hasMany(ScheduleSlot::class);
    }

    public function cells(): HasMany
    {
        return $this->hasMany(ScheduleCell::class);
    }
}
