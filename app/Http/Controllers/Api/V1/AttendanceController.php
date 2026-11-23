<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAttendanceApiRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\HomeroomAssignment;
use App\Models\StudentEnrollment;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Services\Akademik\AttendanceService;
use App\Support\Api\ApiAcademicYear;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiLookup;
use App\Support\Api\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendance) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Attendance::class);

        $validated = $request->validate([
            'kelas' => ['required', 'string', 'max:50'],
            'bulan' => ['nullable', 'date_format:Y-m'],
        ]);

        $year = ApiAcademicYear::active();
        $classGroup = ApiLookup::classGroup($validated['kelas']);
        $month = isset($validated['bulan'])
            ? Carbon::createFromFormat('!Y-m', $validated['bulan'])->startOfMonth()
            : now()->startOfMonth();

        $recap = $this->attendance->monthlyRecap($year, $classGroup, $month);

        return ApiResponse::success([
            'academic_year' => ['id' => $year->id, 'name' => $year->name, 'semester' => $year->semester],
            'class_group' => ['id' => $classGroup->id, 'name' => $classGroup->name],
            'month' => $recap['month']->format('Y-m'),
            'days_in_month' => $recap['days_in_month'],
            'reviewed_count' => $recap['reviewed_count'],
            'summary' => $recap['summary'],
            'rows' => $recap['rows']->map(fn ($row) => [
                'enrollment_id' => $row['enrollment']->id,
                'student' => [
                    'id' => $row['student']?->id,
                    'nis' => $row['student']?->nis,
                    'name' => $row['student']?->displayName(),
                ],
                'cells' => $row['cells'],
                'tally' => $row['tally'],
                'jumlah' => $row['jumlah'],
                'persentase_kehadiran' => $row['persentase_kehadiran'],
            ])->values(),
        ], [
            'filters' => ['kelas' => $classGroup->name, 'bulan' => $month->format('Y-m')],
        ], ['Hari yang belum direview ditampilkan sebagai null dan tidak dihitung Alpha.']);
    }

    public function store(StoreAttendanceApiRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $year = ApiAcademicYear::active();
        $classGroup = ApiLookup::classGroup($validated['class_group_id']);
        $date = Carbon::parse($validated['attendance_date']);

        if (! $this->attendance->isDateEditable($request->user(), $date)) {
            throw new ApiException(
                ApiErrorCode::ATTENDANCE_DATE_LOCKED,
                'Kehadiran tanggal ini terkunci. Hubungi Kepala Madrasah / Wakamad untuk membuka.',
                422,
            );
        }

        $this->assertCanManageClass($request->user(), $year, $classGroup);

        $enrollmentIds = array_map('intval', array_keys($validated['attendances']));
        $validEnrollmentIds = StudentEnrollment::query()
            ->whereIn('id', $enrollmentIds)
            ->where('academic_year_id', $year->id)
            ->where('class_group_id', $classGroup->id)
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $invalid = array_values(array_diff($enrollmentIds, $validEnrollmentIds));

        if ($invalid !== []) {
            throw ApiException::validation(
                'Sebagian enrollment tidak valid untuk kelas/tahun ajaran ini.',
                ['attendances' => ['Enrollment tidak valid: '.implode(', ', $invalid)]],
            );
        }

        $counts = $this->attendance->saveDaily(
            $year,
            $validated['attendance_date'],
            $validated['attendances'],
            $request->user(),
        );

        ApiAudit::log($request, 'api_kehadiran_disimpan', $classGroup, [
            'academic_year_id' => $year->id,
            'attendance_date' => $validated['attendance_date'],
            ...$counts,
        ]);

        return ApiResponse::success([
            'academic_year' => ['id' => $year->id, 'name' => $year->name],
            'class_group' => ['id' => $classGroup->id, 'name' => $classGroup->name],
            'attendance_date' => $validated['attendance_date'],
            'created' => $counts['created'],
            'updated' => $counts['updated'],
            'reviewed' => true,
        ], warnings: ['Satu kelas dianggap sudah direview setelah penyimpanan.'], status: $counts['created'] > 0 ? 201 : 200);
    }

    private function assertCanManageClass(User $user, AcademicYear $year, ClassGroup $classGroup): void
    {
        if (in_array($user->role, AttendanceService::PRIVILEGED_ROLES, true)) {
            return;
        }

        if ($user->role === 'guru' && TeacherAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $year->id)
            ->where('class_group_id', $classGroup->id)
            ->exists()) {
            return;
        }

        if ($user->role === 'wali_kelas' && HomeroomAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $year->id)
            ->where('class_group_id', $classGroup->id)
            ->where('status', 'aktif')
            ->exists()) {
            return;
        }

        throw ApiException::forbidden('Anda tidak mengampu kelas ini.');
    }
}
