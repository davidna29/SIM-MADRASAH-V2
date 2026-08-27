<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpiExamParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_period_id',
        'student_id',
        'exam_room_id',
        'group_id',
        'class_group_id',
        'no_urut',
        'status',
        'jumlah_p1',
        'rata_p1',
        'jumlah_p2',
        'rata_p2',
        'jumlah_p3',
        'rata_p3',
        'jumlah_ujian_lisan',
        'rata_ujian_lisan',
        'rata_hafalan',
        'nilai_akhir',
        'predicate_scale_id',
        'status_lulus',
        'rank_total',
        'rank_lokal',
    ];

    protected function casts(): array
    {
        return [
            'no_urut' => 'integer',
            'jumlah_p1' => 'integer',
            'rata_p1' => 'float',
            'jumlah_p2' => 'integer',
            'rata_p2' => 'float',
            'jumlah_p3' => 'integer',
            'rata_p3' => 'float',
            'jumlah_ujian_lisan' => 'integer',
            'rata_ujian_lisan' => 'float',
            'rata_hafalan' => 'float',
            'nilai_akhir' => 'float',
            'status_lulus' => 'boolean',
            'rank_total' => 'integer',
            'rank_lokal' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpiExamPeriod::class, 'exam_period_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(PpiExamRoom::class, 'exam_room_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(PpiExamGroup::class, 'group_id');
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function predicateScale(): BelongsTo
    {
        return $this->belongsTo(PpiExamPredicateScale::class, 'predicate_scale_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(PpiExamScore::class, 'participant_id');
    }

    public function hafalanScores(): HasMany
    {
        return $this->hasMany(PpiExamHafalanScore::class, 'participant_id');
    }
}
