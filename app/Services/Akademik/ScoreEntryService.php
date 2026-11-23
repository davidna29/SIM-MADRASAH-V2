<?php

namespace App\Services\Akademik;

use App\Models\AcademicYear;
use App\Models\Report;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\ScoreComponentValue;
use App\Models\StudentEnrollment;
use App\Models\TeacherAssignment;
use App\Support\Nilai;
use Illuminate\Support\Collection;

class ScoreEntryService
{
    /**
     * @return Collection<int, ScoreComponent>
     */
    public function componentsFor(int $academicYearId): Collection
    {
        return ScoreComponent::query()
            ->where('academic_year_id', $academicYearId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{created: int, updated: int, cleared: int, mode: string}
     */
    public function save(TeacherAssignment $assignment, AcademicYear $year, array $payload): array
    {
        $enrollments = StudentEnrollment::query()
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('class_group_id', $assignment->class_group_id)
            ->get();

        $components = $this->componentsFor($assignment->academic_year_id);
        $created = 0;
        $updated = 0;
        $cleared = 0;

        if ($components->isNotEmpty()) {
            foreach ($enrollments as $enrollment) {
                $values = $payload['values'][$enrollment->id] ?? [];
                $hasAny = collect($values)->filter(fn ($value) => $value !== null)->isNotEmpty();
                $existing = $this->findScore($assignment, $year, $enrollment);

                if (! $hasAny) {
                    if ($existing) {
                        $existing->delete();
                        $cleared++;
                    }

                    continue;
                }

                $score = Score::updateOrCreate(
                    $this->scoreKey($assignment, $year, $enrollment),
                    ['score' => null],
                );

                foreach ($components as $component) {
                    $value = $values[$component->id] ?? null;

                    if ($value === null) {
                        ScoreComponentValue::query()
                            ->where('score_id', $score->id)
                            ->where('score_component_id', $component->id)
                            ->delete();

                        continue;
                    }

                    $score->componentValues()->updateOrCreate(
                        ['score_component_id' => $component->id],
                        ['value' => $value],
                    );
                }

                $score->update(['score' => $score->computeFinalScore()]);
                $score->wasRecentlyCreated ? $created++ : $updated++;
            }

            return ['created' => $created, 'updated' => $updated, 'cleared' => $cleared, 'mode' => 'components'];
        }

        foreach ($enrollments as $enrollment) {
            $value = $payload['scores'][$enrollment->id] ?? null;
            $existing = $this->findScore($assignment, $year, $enrollment);

            if ($value === null) {
                if ($existing) {
                    $existing->delete();
                    $cleared++;
                }

                continue;
            }

            $score = Score::updateOrCreate(
                $this->scoreKey($assignment, $year, $enrollment),
                ['score' => $value],
            );

            $score->wasRecentlyCreated ? $created++ : $updated++;
        }

        return ['created' => $created, 'updated' => $updated, 'cleared' => $cleared, 'mode' => 'legacy'];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function preview(TeacherAssignment $assignment, AcademicYear $year, array $payload): array
    {
        $enrollments = StudentEnrollment::with('student')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('class_group_id', $assignment->class_group_id)
            ->orderBy('student_id')
            ->get();

        $components = $this->componentsFor($assignment->academic_year_id);
        $mode = $components->isNotEmpty() ? 'components' : 'legacy';
        $items = [];
        $counts = ['changed' => 0, 'unchanged' => 0, 'cleared' => 0];

        foreach ($enrollments as $enrollment) {
            $score = $this->findScore($assignment, $year, $enrollment);
            $before = $score?->score;
            $componentValues = [];

            if ($mode === 'components') {
                $input = $payload['values'][$enrollment->id] ?? [];
                $weighted = $components->map(function (ScoreComponent $component) use ($input, &$componentValues) {
                    $value = $input[$component->id] ?? null;
                    $componentValues[$component->id] = $value;

                    return ['value' => $value, 'weight' => (float) $component->weight];
                });
                $after = Nilai::finalScore($weighted);
            } else {
                $after = $payload['scores'][$enrollment->id] ?? null;
            }

            $status = match (true) {
                $after === $before => 'unchanged',
                $after === null => 'cleared',
                default => 'changed',
            };
            $counts[$status]++;

            $items[] = [
                'enrollment_id' => $enrollment->id,
                'student' => [
                    'id' => $enrollment->student?->id,
                    'nis' => $enrollment->student?->nis,
                    'name' => $enrollment->student?->displayName(),
                ],
                'before' => $before,
                'after' => $after,
                'status' => $status,
                'components' => $mode === 'components' ? $componentValues : null,
            ];
        }

        return [
            'mode' => $mode,
            'dry_run' => true,
            'subject' => [
                'id' => $assignment->subject?->id,
                'code' => $assignment->subject?->code,
                'name' => $assignment->subject?->name,
            ],
            'class_group' => [
                'id' => $assignment->classGroup?->id,
                'name' => $assignment->classGroup?->name,
            ],
            'semester' => $year->semester,
            'counts' => $counts,
            'items' => $items,
        ];
    }

    public function hasPublishedReport(TeacherAssignment $assignment, AcademicYear $year): bool
    {
        $studentIds = StudentEnrollment::query()
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('class_group_id', $assignment->class_group_id)
            ->pluck('student_id');

        return Report::query()
            ->whereIn('student_id', $studentIds)
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('semester', $year->semester)
            ->where('status', 'terbit')
            ->exists();
    }

    private function findScore(TeacherAssignment $assignment, AcademicYear $year, StudentEnrollment $enrollment): ?Score
    {
        return Score::query()
            ->where('student_enrollment_id', $enrollment->id)
            ->where('subject_id', $assignment->subject_id)
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('semester', $year->semester)
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function scoreKey(TeacherAssignment $assignment, AcademicYear $year, StudentEnrollment $enrollment): array
    {
        return [
            'student_enrollment_id' => $enrollment->id,
            'subject_id' => $assignment->subject_id,
            'academic_year_id' => $assignment->academic_year_id,
            'semester' => $year->semester,
        ];
    }
}
