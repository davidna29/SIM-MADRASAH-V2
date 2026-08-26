<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Student extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['person_id', 'nis', 'name', 'gender'];

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
        return $this->belongsToMany(Guardian::class, 'guardian_student');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function displayName(): string
    {
        return $this->person?->name ?? $this->name;
    }
}
