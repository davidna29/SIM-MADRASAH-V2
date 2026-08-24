<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TeachingJournal extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'academic_year_id', 'teacher_assignment_id', 'journal_date', 'period_no',
        'materi', 'tujuan', 'metode', 'catatan', 'tindak_lanjut',
        'lampiran', 'status', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'journal_date' => 'date',
            'period_no' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['journal_date', 'period_no', 'materi', 'tujuan', 'metode', 'catatan', 'tindak_lanjut', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TeacherAssignment::class, 'teacher_assignment_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
