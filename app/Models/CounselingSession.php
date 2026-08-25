<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_enrollment_id',
        'counselor_user_id',
        'session_date',
        'counseling_type',
        'topic',
        'problem_description',
        'assessment_result',
        'action_taken',
        'follow_up_plan',
        'confidentiality_level',
        'status',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_user_id');
    }

    public function scopeForCounselor($query, int $userId)
    {
        return $query->where('counselor_user_id', $userId);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->role === 'super_admin') {
            return $query;
        }

        if ($user->role === 'guru_bk') {
            return $query->where('counselor_user_id', $user->id);
        }

        if ($user->role === 'kepala_madrasah') {
            return $query->whereIn('confidentiality_level', ['plus_kepala', 'plus_wali_kelas']);
        }

        if ($user->role === 'wali_kelas') {
            return $query->where('confidentiality_level', 'plus_wali_kelas');
        }

        return $query->whereRaw('1 = 0');
    }
}
