<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamHafalanMateri extends Model
{
    use HasFactory;

    protected $table = 'ppi_exam_hafalan_materi';

    protected $fillable = ['exam_period_id', 'nama', 'urutan'];

    protected function casts(): array
    {
        return ['urutan' => 'integer'];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }
}
