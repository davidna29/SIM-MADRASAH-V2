<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembiasaanMateri extends Model
{
    use HasFactory;

    protected $table = 'pembiasaan_materi';

    protected $fillable = ['modul', 'no_urut', 'nama_materi', 'jenis'];

    public function periodes(): HasMany
    {
        return $this->hasMany(PembiasaanMateriPeriode::class, 'materi_id');
    }

    public function nilai(): HasMany
    {
        return $this->hasMany(PembiasaanNilai::class, 'materi_id');
    }

    public function scopeForModul($query, string $modul)
    {
        return $query->where('modul', $modul)->orderBy('no_urut');
    }
}
