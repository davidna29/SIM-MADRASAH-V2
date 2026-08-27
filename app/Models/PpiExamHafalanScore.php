<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpiExamHafalanScore extends Model
{
    use HasFactory;

    protected $fillable = ['participant_id', 'hafalan_materi_id', 'nilai', 'tanggal_setor', 'dinilai_oleh_employee_id'];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'tanggal_setor' => 'date',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(PpiExamParticipant::class, 'participant_id');
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(PpiExamHafalanMateri::class, 'hafalan_materi_id');
    }

    public function dinilaiOleh(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'dinilai_oleh_employee_id');
    }
}
