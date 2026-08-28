<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class MutasiRegistration extends Model
{
    use LogsActivity;

    protected $fillable = [
        'registration_no', 'status', 'rejection_reason', 'notes',
        'academic_year_id', 'student_id', 'ip_address',
        'name', 'nik', 'nisn', 'nis_asal', 'gender', 'religion', 'birth_place', 'birth_date',
        'origin_school', 'origin_nsm', 'origin_npsn', 'origin_address', 'kelas_asal',
        'kelas_tujuan', 'alasan_pindah', 'tanggal_mutasi',
        'address', 'province', 'city', 'district', 'village', 'rt', 'rw', 'postal_code',
        'student_phone', 'student_email',
        'father_name', 'father_nik', 'father_job', 'father_phone',
        'mother_name', 'mother_nik', 'mother_job', 'mother_phone',
        'guardian_name', 'guardian_nik', 'guardian_phone',
        'scanned_rekomendasi', 'scanned_rapor', 'scanned_kk', 'scanned_akta', 'scanned_photo',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'tanggal_mutasi' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'kelas_tujuan', 'kelas_asal'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Generate registration number: MUT-YYYY-NNN
     */
    public static function generateRegistrationNo(): string
    {
        $year = now()->year;
        $prefix = "MUT-{$year}-";
        $last = static::where('registration_no', 'like', $prefix.'%')
            ->orderByDesc('registration_no')
            ->first();

        if ($last) {
            $num = (int) substr($last->registration_no, -3) + 1;
        } else {
            $num = 1;
        }

        return $prefix.str_pad((string) $num, 3, '0', STR_PAD_LEFT);
    }
}
