<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class StudentMutation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'student_id', 'academic_year_id', 'tanggal_mutasi',
        'sekolah_tujuan', 'tujuan_nsm', 'tujuan_npsn',
        'alasan_pindah', 'keterangan', 'no_surat', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mutasi' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['sekolah_tujuan', 'alasan_pindah', 'tanggal_mutasi'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Label Bahasa Indonesia untuk alasan pindah. */
    public function alasanLabel(): string
    {
        return match ($this->alasan_pindah) {
            'pindah_ortu' => 'Mengikuti orang tua',
            'pindah_alamat' => 'Pindah alamat / domisili',
            'keluarga' => 'Alasan keluarga',
            'lainnya' => 'Lainnya',
            default => ucfirst($this->alasan_pindah),
        };
    }
}
