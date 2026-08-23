<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ScheduleCell extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'schedule_cells';

    protected $fillable = [
        'schedule_model_id', 'academic_year_id', 'class_group_id',
        'day', 'period_no', 'teacher_id', 'subject_id',
    ];

    protected function casts(): array
    {
        return ['period_no' => 'integer'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['day', 'period_no', 'teacher_id', 'subject_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function scheduleModel(): BelongsTo
    {
        return $this->belongsTo(ScheduleModel::class);
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
