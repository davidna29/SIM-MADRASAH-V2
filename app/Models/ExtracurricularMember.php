<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtracurricularMember extends Model
{
    use HasFactory;

    protected $table = 'extracurricular_members';

    protected $fillable = [
        'extracurricular_id', 'student_enrollment_id', 'tanggal_bergabung',
    ];

    protected function casts(): array
    {
        return ['tanggal_bergabung' => 'date'];
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class, 'extracurricular_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }
}
