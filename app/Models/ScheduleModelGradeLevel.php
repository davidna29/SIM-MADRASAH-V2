<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleModelGradeLevel extends Model
{
    use HasFactory;

    protected $table = 'schedule_model_grade_levels';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['schedule_model_id', 'grade_level'];

    public function scheduleModel(): BelongsTo
    {
        return $this->belongsTo(ScheduleModel::class);
    }
}
