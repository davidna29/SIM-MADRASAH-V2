<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PpdbRegistration extends Model
{
    use LogsActivity;

    protected $fillable = [
        'registration_no', 'status', 'rejection_reason', 'notes',
        'academic_year_id', 'student_id', 'ip_address',
        // A. Data Siswa
        'name', 'nik', 'nisn', 'gender', 'religion', 'birth_place', 'birth_date',
        'previous_school', 'hobby', 'ambition', 'child_order', 'sibling_count',
        'ever_tk', 'ever_paud', 'entry_date',
        // Dokumen
        'scanned_kk', 'scanned_kk_wali', 'scanned_akta', 'scanned_ijazah', 'scanned_photo',
        // B. Imunisasi
        'imm_hepb', 'imm_polio', 'imm_bcg', 'imm_campak', 'imm_dpt', 'imm_covid',
        // C. Berkebutuhan Khusus
        'dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual',
        'dis_behavioral', 'dis_slow_learner', 'dis_communication', 'dis_gifted',
        // D. Alamat Siswa
        'residence_type', 'address', 'province', 'city', 'district', 'village',
        'rt', 'rw', 'postal_code', 'distance', 'transport', 'commute_time',
        'home_phone', 'student_phone', 'student_email',
        // E. Orang Tua
        'kk_number', 'kk_head_name',
        'father_name', 'father_status', 'father_nik', 'father_birth_place',
        'father_birth_date', 'father_education', 'father_job', 'father_income', 'father_phone',
        'mother_name', 'mother_status', 'mother_nik', 'mother_birth_place',
        'mother_birth_date', 'mother_education', 'mother_job', 'mother_income', 'mother_phone',
        'guardian_name', 'guardian_nik', 'guardian_birth_place', 'guardian_birth_date',
        'guardian_education', 'guardian_job', 'guardian_income', 'guardian_phone',
        'social_kks', 'social_pkh', 'social_kip',
        // F. Alamat Orang Tua
        'parent_ownership', 'parent_address', 'parent_province', 'parent_city',
        'parent_district', 'parent_village', 'parent_rt', 'parent_rw', 'parent_postal_code',
        // G. Sekolah Asal
        'origin_school', 'origin_nsm', 'origin_npsn', 'origin_address',
        // Admin-only
        'kelas', 'rombel', 'nis_nism', 'nis_last6',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'father_birth_date' => 'date',
        'mother_birth_date' => 'date',
        'guardian_birth_date' => 'date',
        'entry_date' => 'date',
        'dis_deaf' => 'boolean',
        'dis_blind' => 'boolean',
        'dis_disabled' => 'boolean',
        'dis_intellectual' => 'boolean',
        'dis_behavioral' => 'boolean',
        'dis_slow_learner' => 'boolean',
        'dis_communication' => 'boolean',
        'dis_gifted' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'kelas', 'rombel', 'nis_nism'])
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
     * Generate registration number: PPDB-YYYY-NNN
     */
    public static function generateRegistrationNo(): string
    {
        $year = now()->year;
        $prefix = "PPDB-{$year}-";
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
