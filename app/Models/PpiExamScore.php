<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamScore extends Model
{
    use HasFactory;

    protected $fillable = ['participant_id', 'aspect_id', 'nilai', 'examiner_employee_id', 'input_at'];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'input_at' => 'datetime',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(PpiExamParticipant::class, 'participant_id');
    }

    public function aspect(): BelongsTo
    {
        return $this->belongsTo(PpiExamAspect::class, 'aspect_id');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'examiner_employee_id');
    }
}
