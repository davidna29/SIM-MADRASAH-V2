<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';

    protected $fillable = [
        'title', 'jenis', 'tanggal', 'waktu', 'lokasi', 'penanggung_jawab',
        'isi', 'target', 'tampil_mulai', 'tampil_selesai', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu' => 'datetime:H:i',
            'tampil_mulai' => 'date',
            'tampil_selesai' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isTampil(): bool
    {
        $today = today();

        return $this->status === 'aktif'
            && $today->greaterThanOrEqualTo($this->tampil_mulai)
            && ($this->tampil_selesai === null || $today->lessThanOrEqualTo($this->tampil_selesai));
    }
}
