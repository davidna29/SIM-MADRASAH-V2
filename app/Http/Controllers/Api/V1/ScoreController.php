<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateScoresRequest;
use App\Models\Score;
use App\Models\StudentEnrollment;
use App\Models\TeacherAssignment;
use App\Services\Akademik\ScoreEntryService;
use App\Support\Api\ApiAcademicYear;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiLookup;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function __construct(private readonly ScoreEntryService $scores) {}

    public function index(Request $request): JsonResponse
    {
        $allowed = ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah', 'guru', 'wali_kelas'];
        if (! in_array($request->user()->role, $allowed, true)) {
            throw ApiException::forbidden('Role Anda tidak memiliki akses ke data nilai.');
        }

        $validated = $request->validate([
            'kelas' => ['required', 'string', 'max:50'],
            'mapel' => ['nullable', 'string', 'max:100'],
        ]);

        $year = ApiAcademicYear::active();
        $classGroup = ApiLookup::classGroup($validated['kelas']);
        $subject = isset($validated['mapel']) ? ApiLookup::subject($validated['mapel']) : null;
        $perPage = min(max((int) $request->integer('per_page', 50), 1), 100);

        $ownedSubjectIds = null;
        if ($request->user()->role === 'guru') {
            $ownedSubjectIds = TeacherAssignment::query()
                ->where('user_id', $request->user()->id)
                ->where('academic_year_id', $year->id)
                ->where('class_group_id', $classGroup->id)
                ->pluck('subject_id');

            if ($ownedSubjectIds->isEmpty()) {
                throw ApiException::forbidden('Anda tidak mengampu kelas ini.');
            }
        }

        $enrollmentIds = StudentEnrollment::query()
            ->where('academic_year_id', $year->id)
            ->where('class_group_id', $classGroup->id)
            ->pluck('id');

        $scores = Score::query()
            ->with([
                'subject:id,code,name',
                'enrollment:id,student_id,class_group_id',
                'enrollment.student:id,person_id,nis,name',
                'componentValues.scoreComponent:id,name,weight',
            ])
            ->where('academic_year_id', $year->id)
            ->where('semester', $year->semester)
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->when($subject, fn ($query) => $query->where('subject_id', $subject->id))
            ->when($ownedSubjectIds, fn ($query) => $query->whereIn('subject_id', $ownedSubjectIds))
            ->orderBy('subject_id')
            ->orderBy('student_enrollment_id')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $scores,
            $scores->getCollection()->map(fn (Score $score) => [
                'enrollment_id' => $score->student_enrollment_id,
                'student' => [
                    'id' => $score->enrollment?->student?->id,
                    'nis' => $score->enrollment?->student?->nis,
                    'name' => $score->enrollment?->student?->displayName(),
                ],
                'subject' => [
                    'id' => $score->subject?->id,
                    'code' => $score->subject?->code,
                    'name' => $score->subject?->name,
                ],
                'score' => $score->score,
                'components' => $score->componentValues->map(fn ($value) => [
                    'component_id' => $value->score_component_id,
                    'name' => $value->scoreComponent?->name,
                    'weight' => $value->scoreComponent?->weight,
                    'value' => $value->value,
                ])->values(),
            ])->all(),
            [
                'filters' => [
                    'kelas' => $classGroup->name,
                    'mapel' => $subject?->name,
                    'academic_year_id' => $year->id,
                    'semester' => $year->semester,
                ],
            ],
            ['Hanya nilai yang sudah tersimpan yang ditampilkan.'],
        );
    }

    public function store(UpdateScoresRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $year = ApiAcademicYear::active();
        $assignment = TeacherAssignment::with(['subject', 'classGroup'])->findOrFail($validated['teacher_assignment_id']);

        if ($request->user()->role !== 'guru' || $assignment->user_id !== $request->user()->id) {
            throw ApiException::forbidden('Anda tidak memiliki penugasan mengajar ini.');
        }

        if ($assignment->academic_year_id !== $year->id) {
            throw ApiException::business('Penugasan bukan untuk tahun ajaran aktif.');
        }

        $components = $this->scores->componentsFor($assignment->academic_year_id);

        if ($components->isNotEmpty() && ! isset($validated['values'])) {
            throw ApiException::validation('Gunakan payload values untuk komponen nilai tahun ini.', [
                'values' => ['Payload values wajib dikirim.'],
            ]);
        }

        if ($components->isEmpty() && ! isset($validated['scores'])) {
            throw ApiException::validation('Gunakan payload scores untuk nilai tanpa komponen.', [
                'scores' => ['Payload scores wajib dikirim.'],
            ]);
        }

        if ($this->scores->hasPublishedReport($assignment, $year)) {
            throw ApiException::conflict(
                'Rapor semester ini sudah terbit; nilai tidak dapat diubah melalui API.',
                ApiErrorCode::RECORD_ALREADY_PUBLISHED,
            );
        }

        if ($request->boolean('dry_run')) {
            return ApiResponse::success(
                $this->scores->preview($assignment, $year, $validated),
                warnings: ['Ini hanya pratinjau; tidak ada nilai yang disimpan.'],
            );
        }

        $counts = $this->scores->save($assignment, $year, $validated);

        ApiAudit::log($request, 'api_nilai_disimpan', $assignment, [
            'academic_year_id' => $year->id,
            'semester' => $year->semester,
            ...$counts,
        ]);

        return ApiResponse::success([
            'assignment' => [
                'id' => $assignment->id,
                'subject' => $assignment->subject?->name,
                'class_group' => $assignment->classGroup?->name,
            ],
            'mode' => $counts['mode'],
            'created' => $counts['created'],
            'updated' => $counts['updated'],
            'cleared' => $counts['cleared'],
        ], warnings: ['Nilai akhir dihitung dari komponen berbobot.'], status: $counts['created'] > 0 ? 201 : 200);
    }
}
