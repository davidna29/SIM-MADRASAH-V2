<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_enrollment_id', 'academic_year_id', 'semester', 'bulan',
        'nominal', 'status', 'tanggal_bayar', 'metode', 'catatan', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'bulan' => 'integer',
            'tanggal_bayar' => 'date',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }
}
