<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Employee extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'person_id', 'organizational_unit_id', 'position_id', 'user_id',
        'nip', 'employee_status', 'status', 'tmt',
    ];

    protected function casts(): array
    {
        return ['tmt' => 'date'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nip', 'employee_status', 'status', 'tmt', 'position_id', 'organizational_unit_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function positionHistories(): HasMany
    {
        return $this->hasMany(EmployeePositionHistory::class);
    }

    public function positionLabel(): string
    {
        return $this->position?->name ?? '—';
    }
}
