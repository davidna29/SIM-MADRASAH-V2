<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembiasaanNilai extends Model
{
    use HasFactory;

    protected $table = 'pembiasaan_nilai';

    protected $fillable = ['siswa_id', 'materi_id', 'kelas', 'semester', 'tahun_pelajaran', 'nilai'];

    protected function casts(): array
    {
        return ['nilai' => 'integer'];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(PembiasaanMateri::class, 'materi_id');
    }
}
