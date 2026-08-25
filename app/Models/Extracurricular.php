<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Extracurricular extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'slug', 'description', 'pembina_id',
        'hari', 'waktu', 'lokasi', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['waktu' => 'datetime:H:i'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'status', 'pembina_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembina_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ExtracurricularMember::class, 'extracurricular_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ExtracurricularAttendance::class, 'extracurricular_id');
    }
}
