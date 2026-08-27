<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Room extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'rooms';

    public const TYPES = ['ruangan' => 'Ruangan', 'laboratorium' => 'Laboratorium'];

    public const CONDITIONS = ['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat', 'dalam_perbaikan' => 'Dalam Perbaikan'];

    protected $fillable = [
        'code', 'name', 'type', 'building', 'floor', 'capacity',
        'employee_id', 'condition', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'type', 'building', 'floor', 'capacity', 'condition', 'employee_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function penanggJawab(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conditionLabel(): string
    {
        return self::CONDITIONS[$this->condition] ?? $this->condition;
    }

    public static function nextCode(): string
    {
        $last = static::orderByDesc('id')->value('code');
        $next = $last ? ((int) substr($last, 2) + 1) : 1;

        return 'R-'.str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
