<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpiExamAspectCategory extends Model
{
    use HasFactory;

    protected $fillable = ['exam_period_id', 'kode', 'nama', 'penguji_urutan', 'urutan'];

    protected function casts(): array
    {
        return [
            'penguji_urutan' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }

    public function aspects(): HasMany
    {
        return $this->hasMany(PpiExamAspect::class, 'aspect_category_id')->orderBy('urutan');
    }
}
