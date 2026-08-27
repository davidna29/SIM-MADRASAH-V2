<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_period_id',
        'nisn',
        'nama_siswa',
        'rata_p1',
        'rata_p2',
        'rata_p3',
        'rata_hafalan',
        'nilai_akhir',
        'predikat',
        'deskripsi',
        'status_lulus',
        'rank',
        'rombel',
    ];

    protected function casts(): array
    {
        return [
            'rata_p1' => 'float',
            'rata_p2' => 'float',
            'rata_p3' => 'float',
            'rata_hafalan' => 'float',
            'nilai_akhir' => 'float',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }
}
