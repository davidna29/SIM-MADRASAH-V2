<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpiExamRoom extends Model
{
    use HasFactory;

    protected $fillable = ['exam_period_id', 'nama'];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(PpiExamExaminer::class, 'exam_room_id')->orderBy('urutan');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(PpiExamParticipant::class, 'exam_room_id')->orderBy('no_urut');
    }
}
