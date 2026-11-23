<?php

namespace App\Services\Akademik;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceReview;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceService
{
    public const STATUSES = ['hadir', 'izin', 'sakit', 'alpha'];

    public const PRIVILEGED_ROLES = ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan'];

    public function isDateEditable(User $user, Carbon $date): bool
    {
        return $date->isToday() || in_array($user->role, self::PRIVILEGED_ROLES, true);
    }

    /**
     * @param  array<int|string, array{status?: string|null, note?: string|null}>  $attendances
     * @return array{created: int, updated: int, class_group_ids: array<int, int>}
     */
    public function saveDaily(AcademicYear $year, string $date, array $attendances, User $user): array
    {
        $enrollmentIds = array_map('intval', array_keys($attendances));
        $existing = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->where('attendance_date', $date)
            ->pluck('student_enrollment_id')
            ->all();
        $existing = array_flip(array_map('intval', $existing));

        $created = 0;
        $updated = 0;
        $classGroupIds = [];

        foreach ($attendances as $enrollmentId => $data) {
            $enrollment = StudentEnrollment::find($enrollmentId);

            if (! $enrollment) {
                continue;
            }

            $classGroupIds[$enrollment->class_group_id] = $enrollment->class_group_id;
            $status = $data['status'] ?? 'hadir';
            $note = $data['note'] ?? null;

            Attendance::updateOrCreate(
                [
                    'student_enrollment_id' => $enrollmentId,
                    'attendance_date' => $date,
                ],
                [
                    'academic_year_id' => $year->id,
                    'class_group_id' => $enrollment->class_group_id,
                    'status' => $status,
                    'note' => $note ?: null,
                    'recorded_by' => $user->id,
                ],
            );

            isset($existing[(int) $enrollmentId]) ? $updated++ : $created++;
        }

        foreach ($classGroupIds as $classGroupId) {
            AttendanceReview::updateOrCreate(
                [
                    'class_group_id' => $classGroupId,
                    'attendance_date' => $date,
                ],
                [
                    'academic_year_id' => $year->id,
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now(),
                ],
            );
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'class_group_ids' => array_values($classGroupIds),
        ];
    }

    /**
     * @return array{
     *     rows: Collection<int, array<string, mixed>>,
     *     reviewed_count: int,
     *     summary: array<string, int|float|null>,
     *     month: Carbon,
     *     days_in_month: int
     * }
     */
    public function monthlyRecap(AcademicYear $year, ClassGroup $classGroup, Carbon $month): array
    {
        $month = $month->copy()->startOfMonth();
        $daysInMonth = $month->daysInMonth;

        $enrollments = StudentEnrollment::with('student')
            ->where('academic_year_id', $year->id)
            ->where('class_group_id', $classGroup->id)
            ->where('status', 'aktif')
            ->orderBy('student_id')
            ->get();

        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $reviewedDates = AttendanceReview::query()
            ->where('class_group_id', $classGroup->id)
            ->whereBetween('attendance_date', [$start, $end])
            ->pluck('attendance_date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->flip();

        $reviewedCount = $reviewedDates->count();

        $attendances = Attendance::query()
            ->where('class_group_id', $classGroup->id)
            ->whereBetween('attendance_date', [$start, $end])
            ->get()
            ->groupBy(fn ($attendance) => $attendance->student_enrollment_id.'|'.$attendance->attendance_date->format('Y-m-d'));

        $rows = $enrollments->map(function (StudentEnrollment $enrollment) use ($attendances, $reviewedDates, $month, $daysInMonth, $reviewedCount) {
            $cells = [];
            $tally = ['S' => 0, 'I' => 0, 'A' => 0, 'H' => 0];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $month->copy()->day($day)->format('Y-m-d');

                if (! isset($reviewedDates[$date])) {
                    $cells[$day] = null;

                    continue;
                }

                $status = $attendances->get($enrollment->id.'|'.$date)?->first()?->status;
                $mark = match ($status) {
                    'hadir' => '•',
                    'sakit' => 'S',
                    'izin' => 'I',
                    'alpha' => 'A',
                    default => null,
                };

                $cells[$day] = $mark;

                if ($mark === '•') {
                    $tally['H']++;
                } elseif ($mark) {
                    $tally[$mark]++;
                }
            }

            return [
                'enrollment' => $enrollment,
                'student' => $enrollment->student,
                'cells' => $cells,
                'tally' => $tally,
                'jumlah' => $tally['S'] + $tally['I'] + $tally['A'],
                'persentase_kehadiran' => $reviewedCount > 0
                    ? round($tally['H'] / $reviewedCount * 100, 1)
                    : null,
            ];
        });

        $totalStudents = $enrollments->count();
        $totalPresent = $rows->sum(fn ($row) => $row['tally']['H']);
        $totalSick = $rows->sum(fn ($row) => $row['tally']['S']);
        $totalPermit = $rows->sum(fn ($row) => $row['tally']['I']);
        $totalAbsent = $rows->sum(fn ($row) => $row['tally']['A']);
        $totalAbsence = $totalSick + $totalPermit + $totalAbsent;
        $totalSlots = $totalStudents * $reviewedCount;

        return [
            'rows' => $rows,
            'reviewed_count' => $reviewedCount,
            'summary' => [
                'total_sakit' => $totalSick,
                'total_izin' => $totalPermit,
                'total_alfa' => $totalAbsent,
                'total_ketidakhadiran' => $totalAbsence,
                'persentase_ketidakhadiran' => $totalSlots > 0 ? round($totalAbsence / $totalSlots * 100, 1) : null,
                'persentase_kehadiran' => $totalSlots > 0 ? round($totalPresent / $totalSlots * 100, 1) : null,
            ],
            'month' => $month,
            'days_in_month' => $daysInMonth,
        ];
    }
}
