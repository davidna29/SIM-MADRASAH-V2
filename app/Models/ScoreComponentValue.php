<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreComponentValue extends Model
{
    use HasFactory;

    protected $fillable = ['score_id', 'score_component_id', 'value'];

    protected function casts(): array
    {
        return ['value' => 'integer'];
    }

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }

    public function scoreComponent(): BelongsTo
    {
        return $this->belongsTo(ScoreComponent::class);
    }
}
