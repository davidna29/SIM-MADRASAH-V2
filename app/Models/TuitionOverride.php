<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionOverride extends Model
{
    use HasFactory;

    protected $fillable = ['student_enrollment_id', 'academic_year_id', 'nominal', 'keterangan'];

    protected function casts(): array
    {
        return ['nominal' => 'integer'];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
