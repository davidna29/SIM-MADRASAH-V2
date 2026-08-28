<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Student extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'person_id', 'nis', 'name', 'gender',
        // Profil akademik (asal PPDB / data siswa lengkap)
        'nisn', 'previous_school', 'origin_school', 'origin_nsm', 'origin_npsn', 'origin_address',
        'entry_date', 'hobby', 'ambition', 'child_order', 'sibling_count',
        'ever_tk', 'ever_paud', 'residence_type', 'distance', 'transport', 'commute_time',
        'kk_number', 'kk_head_name',
        'social_kks', 'social_pkh', 'social_kip',
        'parent_ownership', 'parent_address', 'parent_province', 'parent_city',
        'parent_district', 'parent_village', 'parent_rt', 'parent_rw', 'parent_postal_code',
        'imm_hepb', 'imm_polio', 'imm_bcg', 'imm_campak', 'imm_dpt', 'imm_covid',
        'dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual',
        'dis_behavioral', 'dis_slow_learner', 'dis_communication', 'dis_gifted',
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nis'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')->withPivot('relation');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function ppdbRegistration(): HasOne
    {
        return $this->hasOne(PpdbRegistration::class);
    }

    /** Guardian dalam relasi tertentu (ayah/ibu/wali) — pakai firstWhere agar aman bila kuncup. */
    public function guardianByRelation(string $relation): ?Guardian
    {
        return $this->guardians->firstWhere('pivot.relation', $relation);
    }

    public function displayName(): string
    {
        return $this->person?->name ?? $this->name;
    }
}
