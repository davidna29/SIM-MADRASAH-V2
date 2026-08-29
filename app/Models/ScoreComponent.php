<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoreComponent extends Model
{
    use HasFactory;

    protected $fillable = ['academic_year_id', 'name', 'weight', 'sort_order'];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ScoreComponentValue::class);
    }
}
