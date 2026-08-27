<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpiExamGroup extends Model
{
    use HasFactory;

    protected $fillable = ['exam_period_id', 'nama', 'pembimbing_employee_id'];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pembimbing_employee_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(PpiExamParticipant::class, 'group_id')->orderBy('no_urut');
    }
}
