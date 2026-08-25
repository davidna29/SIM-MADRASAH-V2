<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offense extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'kategori', 'tingkat', 'poin', 'tanggal_kejadian',
        'kronologi', 'pelapor', 'bukti', 'tindakan', 'pemanggilan_ortu',
        'surat_peringatan', 'status_penyelesaian', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'poin' => 'integer',
            'pemanggilan_ortu' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
