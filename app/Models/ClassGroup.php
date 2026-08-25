<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'grade_level'];

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function homeroom(): HasOne
    {
        return $this->hasOne(HomeroomAssignment::class)
            ->where('academic_year_id', AcademicYear::active()->id)
            ->where('status', 'aktif');
    }

    public function homerooms(): HasMany
    {
        return $this->hasMany(HomeroomAssignment::class);
    }
}
