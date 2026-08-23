<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleSlot extends Model
{
    use HasFactory;

    protected $table = 'schedule_model_slots';

    protected $fillable = ['schedule_model_id', 'period_no', 'start_time', 'end_time', 'is_break', 'label'];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_break' => 'boolean',
        ];
    }

    public function scheduleModel(): BelongsTo
    {
        return $this->belongsTo(ScheduleModel::class);
    }
}
