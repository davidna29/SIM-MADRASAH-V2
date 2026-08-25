<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class InventoryItem extends Model
{
    use HasFactory, LogsActivity;

    public const CONDITIONS = ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'];

    public const STATUSES = ['tersedia', 'dipinjam', 'dalam_perawatan', 'tidak_aktif'];

    protected $fillable = [
        'category_id', 'code', 'name', 'brand', 'model', 'serial_number',
        'quantity', 'unit', 'condition', 'location', 'purchase_date',
        'purchase_price', 'status', 'photo', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'quantity' => 'integer',
            'purchase_price' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'category_id', 'quantity', 'condition', 'status', 'location'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(InventoryMutation::class, 'item_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(InventoryMaintenance::class, 'item_id');
    }
}
