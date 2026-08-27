<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamExaminer extends Model
{
    use HasFactory;

    protected $fillable = ['exam_period_id', 'exam_room_id', 'employee_id', 'urutan'];

    protected function casts(): array
    {
        return ['urutan' => 'integer'];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(PpiExamRoom::class, 'exam_room_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
