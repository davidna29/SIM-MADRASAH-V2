<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtracurricularAttendance extends Model
{
    use HasFactory;

    public const POINTS = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];

    protected $fillable = [
        'extracurricular_id', 'student_enrollment_id', 'tanggal',
        'status', 'predikat', 'keterangan',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class, 'extracurricular_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    /** Predikat akhir dari rata-rata poin (A=4 … D=1). */
    public static function predicateFromAverage(float $average): string
    {
        return match (true) {
            $average >= 3.5 => 'A',
            $average >= 2.5 => 'B',
            $average >= 1.5 => 'C',
            default => 'D',
        };
    }
}
