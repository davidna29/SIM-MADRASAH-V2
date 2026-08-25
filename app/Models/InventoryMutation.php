<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class InventoryMutation extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = ['pending', 'disetujui', 'ditolak'];

    protected $fillable = [
        'item_id', 'from_location', 'to_location', 'quantity',
        'mutation_date', 'reason', 'approved_by', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'mutation_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['item_id', 'from_location', 'to_location', 'quantity', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
