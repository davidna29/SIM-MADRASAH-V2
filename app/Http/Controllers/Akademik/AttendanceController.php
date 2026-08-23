<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Attendance::class);

        $tahun = AcademicYear::active();
        $classGroupId = request('class_group_id');
        $date = request('date') ? Carbon::parse(request('date')) : Carbon::today();

        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $enrollments = collect();
        $attendances = collect();

        if ($classGroupId) {
            $enrollments = StudentEnrollment::with('student.person')
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $classGroupId)
                ->where('status', 'aktif')
                ->orderBy('student_id')
                ->get();

            $attendances = Attendance::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $classGroupId)
                ->where('attendance_date', $date->toDateString())
                ->get()
                ->keyBy('student_enrollment_id');
        }

        return view('pages.kehadiran.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Kehadiran Siswa'],
            ],
            'tahun' => $tahun,
            'classes' => $classes,
            'selectedClass' => $classGroupId ? ClassGroup::find($classGroupId) : null,
            'date' => $date,
            'enrollments' => $enrollments,
            'attendances' => $attendances,
        ]);
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $validated = $request->validated();
        $tahun = AcademicYear::active();

        foreach ($validated['attendances'] as $enrollmentId => $data) {
            $status = $data['status'] ?? 'hadir';
            $note = $data['note'] ?? null;

            $enrollment = StudentEnrollment::find($enrollmentId);

            if (! $enrollment) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'student_enrollment_id' => $enrollmentId,
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'academic_year_id' => $tahun->id,
                    'class_group_id' => $enrollment->class_group_id,
                    'status' => $status,
                    'note' => $note ?: null,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        activity('kesiswaan')->log('kehadiran_diinput');

        return back()->with('status', 'Kehadiran siswa berhasil disimpan dan disematkan ke papan.');
    }
}
