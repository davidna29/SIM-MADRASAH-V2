<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Score extends Model
{
    use HasFactory;

    protected $fillable = ['student_enrollment_id', 'subject_id', 'academic_year_id', 'semester', 'score'];

    protected function casts(): array
    {
        return ['score' => 'integer'];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function componentValues(): HasMany
    {
        return $this->hasMany(ScoreComponentValue::class);
    }

    /**
     * Hitung nilai akhir tertimbang dari komponen.
     * Mengembalikan null bila tidak ada komponen terisi.
     *
     * @param  Collection<int, ScoreComponentValue>|null  $values
     */
    public function computeFinalScore(?Collection $values = null): ?int
    {
        $values ??= $this->componentValues()->with('scoreComponent')->get();

        $weighted = $values->filter(fn ($v) => $v->value !== null)
            ->map(fn ($v) => [
                'value' => $v->value,
                'weight' => (float) $v->scoreComponent->weight,
            ]);

        if ($weighted->isEmpty()) {
            return null;
        }

        $totalWeight = $weighted->sum('weight');

        if ($totalWeight <= 0) {
            return null;
        }

        return (int) round($weighted->sum(fn ($v) => $v['value'] * $v['weight']) / $totalWeight);
    }
}
