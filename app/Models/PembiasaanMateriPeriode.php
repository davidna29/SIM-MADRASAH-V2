<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembiasaanMateriPeriode extends Model
{
    use HasFactory;

    protected $table = 'pembiasaan_materi_periode';

    protected $fillable = ['materi_id', 'kelas', 'semester', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(PembiasaanMateri::class, 'materi_id');
    }
}
