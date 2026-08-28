<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'source_registration_id',
        // Identitas tambahan
        'nisn', 'previous_school', 'origin_school', 'origin_nsm', 'origin_npsn', 'origin_address',
        'entry_date', 'hobby', 'ambition',
        'child_order', 'sibling_count', 'ever_tk', 'ever_paud',
        // Alamat siswa
        'address', 'residence_type', 'province', 'city', 'district', 'village',
        'rt', 'rw', 'postal_code', 'distance', 'transport', 'commute_time', 'home_phone',
        // Keluarga & KK
        'kk_number', 'kk_head_name',
        'father_name', 'father_status', 'father_nik', 'father_birth_place', 'father_birth_date',
        'father_education', 'father_job', 'father_income', 'father_phone',
        'mother_name', 'mother_status', 'mother_nik', 'mother_birth_place', 'mother_birth_date',
        'mother_education', 'mother_job', 'mother_income', 'mother_phone',
        'guardian_name', 'guardian_nik', 'guardian_birth_place', 'guardian_birth_date',
        'guardian_education', 'guardian_job', 'guardian_income', 'guardian_phone',
        // Bantuan sosial
        'social_kks', 'social_pkh', 'social_kip',
        // Alamat orang tua
        'parent_ownership', 'parent_address', 'parent_province', 'parent_city',
        'parent_district', 'parent_village', 'parent_rt', 'parent_rw', 'parent_postal_code',
        // Kesehatan & kebutuhan khusus
        'imm_hepb', 'imm_polio', 'imm_bcg', 'imm_campak', 'imm_dpt', 'imm_covid',
        'dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual',
        'dis_behavioral', 'dis_slow_learner', 'dis_communication', 'dis_gifted',
        // Dokumen
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'father_birth_date' => 'date',
            'mother_birth_date' => 'date',
            'guardian_birth_date' => 'date',
            'child_order' => 'integer',
            'sibling_count' => 'integer',
            'imm_hepb' => 'boolean',
            'imm_polio' => 'boolean',
            'imm_bcg' => 'boolean',
            'imm_campak' => 'boolean',
            'imm_dpt' => 'boolean',
            'imm_covid' => 'boolean',
            'dis_deaf' => 'boolean',
            'dis_blind' => 'boolean',
            'dis_disabled' => 'boolean',
            'dis_intellectual' => 'boolean',
            'dis_behavioral' => 'boolean',
            'dis_slow_learner' => 'boolean',
            'dis_communication' => 'boolean',
            'dis_gifted' => 'boolean',
            'documents' => 'array',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function sourceRegistration(): BelongsTo
    {
        return $this->belongsTo(PpdbRegistration::class, 'source_registration_id');
    }

    /** Daftar imunisasi yang sudah PERNAH (ya=true), untuk badge. */
    public function immunizationsDone(): array
    {
        $labels = [
            'imm_hepb' => 'Hepatitis B',
            'imm_polio' => 'Polio',
            'imm_bcg' => 'BCG',
            'imm_campak' => 'Campak',
            'imm_dpt' => 'DPT-HB-HiB',
            'imm_covid' => 'COVID',
        ];
        $done = [];
        foreach ($labels as $key => $label) {
            if ($this->{$key}) {
                $done[] = $label;
            }
        }

        return $done;
    }

    /** Daftar kebutuhan khusus yang tercentang, untuk badge. */
    public function disabilitiesList(): array
    {
        $labels = [
            'dis_deaf' => 'Tuna Rungu',
            'dis_blind' => 'Tuna Netra',
            'dis_disabled' => 'Tuna Daksa',
            'dis_intellectual' => 'Tuna Grahita',
            'dis_behavioral' => 'Tuna Laras',
            'dis_slow_learner' => 'Lamban Belajar',
            'dis_communication' => 'Gangguan Komunikasi',
            'dis_gifted' => 'Bakat Luar Biasa',
        ];
        $done = [];
        foreach ($labels as $key => $label) {
            if ($this->{$key}) {
                $done[] = $label;
            }
        }

        return $done;
    }

    /**
     * Salin data registrasi PPDB menjadi snapshot profil siswa (idempotent).
     * Satu arah: accept & backfill saja. Edit data di registrasi lalu re-accept/backfill.
     */
    public static function syncFromRegistration(Student $student, PpdbRegistration $r): self
    {
        $data = [
            'source_registration_id' => $r->id,
            'nisn' => $r->nisn,
            'previous_school' => $r->previous_school,
            'origin_school' => $r->origin_school,
            'origin_nsm' => $r->origin_nsm,
            'origin_npsn' => $r->origin_npsn,
            'origin_address' => $r->origin_address,
            'entry_date' => $r->entry_date,
            'hobby' => $r->hobby,
            'ambition' => $r->ambition,
            'child_order' => $r->child_order,
            'sibling_count' => $r->sibling_count,
            'ever_tk' => $r->ever_tk,
            'ever_paud' => $r->ever_paud,
            'address' => $r->address,
            'residence_type' => $r->residence_type,
            'province' => $r->province,
            'city' => $r->city,
            'district' => $r->district,
            'village' => $r->village,
            'rt' => $r->rt,
            'rw' => $r->rw,
            'postal_code' => $r->postal_code,
            'distance' => $r->distance,
            'transport' => $r->transport,
            'commute_time' => $r->commute_time,
            'home_phone' => $r->home_phone,
            'kk_number' => $r->kk_number,
            'kk_head_name' => $r->kk_head_name,
            'father_name' => $r->father_name,
            'father_status' => $r->father_status,
            'father_nik' => $r->father_nik,
            'father_birth_place' => $r->father_birth_place,
            'father_birth_date' => $r->father_birth_date,
            'father_education' => $r->father_education,
            'father_job' => $r->father_job,
            'father_income' => $r->father_income,
            'father_phone' => $r->father_phone,
            'mother_name' => $r->mother_name,
            'mother_status' => $r->mother_status,
            'mother_nik' => $r->mother_nik,
            'mother_birth_place' => $r->mother_birth_place,
            'mother_birth_date' => $r->mother_birth_date,
            'mother_education' => $r->mother_education,
            'mother_job' => $r->mother_job,
            'mother_income' => $r->mother_income,
            'mother_phone' => $r->mother_phone,
            'guardian_name' => $r->guardian_name,
            'guardian_nik' => $r->guardian_nik,
            'guardian_birth_place' => $r->guardian_birth_place,
            'guardian_birth_date' => $r->guardian_birth_date,
            'guardian_education' => $r->guardian_education,
            'guardian_job' => $r->guardian_job,
            'guardian_income' => $r->guardian_income,
            'guardian_phone' => $r->guardian_phone,
            'social_kks' => $r->social_kks,
            'social_pkh' => $r->social_pkh,
            'social_kip' => $r->social_kip,
            'parent_ownership' => $r->parent_ownership,
            'parent_address' => $r->parent_address,
            'parent_province' => $r->parent_province,
            'parent_city' => $r->parent_city,
            'parent_district' => $r->parent_district,
            'parent_village' => $r->parent_village,
            'parent_rt' => $r->parent_rt,
            'parent_rw' => $r->parent_rw,
            'parent_postal_code' => $r->parent_postal_code,
            'imm_hepb' => $r->imm_hepb === 'PERNAH',
            'imm_polio' => $r->imm_polio === 'PERNAH',
            'imm_bcg' => $r->imm_bcg === 'PERNAH',
            'imm_campak' => $r->imm_campak === 'PERNAH',
            'imm_dpt' => $r->imm_dpt === 'PERNAH',
            'imm_covid' => $r->imm_covid === 'PERNAH',
            'dis_deaf' => (bool) $r->dis_deaf,
            'dis_blind' => (bool) $r->dis_blind,
            'dis_disabled' => (bool) $r->dis_disabled,
            'dis_intellectual' => (bool) $r->dis_intellectual,
            'dis_behavioral' => (bool) $r->dis_behavioral,
            'dis_slow_learner' => (bool) $r->dis_slow_learner,
            'dis_communication' => (bool) $r->dis_communication,
            'dis_gifted' => (bool) $r->dis_gifted,
            'documents' => array_filter([
                'kk' => $r->scanned_kk,
                'kk_wali' => $r->scanned_kk_wali,
                'akta' => $r->scanned_akta,
                'ijazah' => $r->scanned_ijazah,
                'photo' => $r->scanned_photo,
            ]),
        ];

        return static::updateOrCreate(
            ['student_id' => $student->id],
            $data
        );
    }
}
