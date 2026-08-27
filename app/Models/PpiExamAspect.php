<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamAspect extends Model
{
    use HasFactory;

    protected $fillable = ['aspect_category_id', 'kode', 'nama', 'urutan'];

    protected function casts(): array
    {
        return ['urutan' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PpiExamAspectCategory::class, 'aspect_category_id');
    }
}
