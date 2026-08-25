<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class InventoryMaintenance extends Model
{
    use HasFactory, LogsActivity;

    public const TYPES = ['perawatan', 'perbaikan'];

    public const STATUSES = ['berlangsung', 'selesai'];

    protected $fillable = [
        'item_id', 'type', 'description', 'cost', 'start_date',
        'end_date', 'technician', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'cost' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['item_id', 'type', 'cost', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
