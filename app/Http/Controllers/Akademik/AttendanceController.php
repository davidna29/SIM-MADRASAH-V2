<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use App\Services\Akademik\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendance) {}

    public function index(): View
    {
        $this->authorize('viewAny', Attendance::class);

        $tahun = AcademicYear::active();
        $classGroupId = request('class_group_id');
        $date = request('date') ? Carbon::parse(request('date')) : Carbon::today();

        $this->assertDateEditable($date);

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
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Kehadiran Siswa'],
            ],
            'tahun' => $tahun,
            'classes' => $classes,
            'selectedClass' => $classGroupId ? ClassGroup::find($classGroupId) : null,
            'date' => $date,
            'editable' => $this->attendance->isDateEditable(auth()->user(), $date),
            'enrollments' => $enrollments,
            'attendances' => $attendances,
        ]);
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $validated = $request->validated();
        $tanggal = Carbon::parse($validated['attendance_date']);

        $this->assertDateEditable($tanggal);

        $this->attendance->saveDaily(
            AcademicYear::active(),
            $validated['attendance_date'],
            $validated['attendances'],
            $request->user(),
        );

        activity('kesiswaan')->log('kehadiran_diinput');

        return back()->with('status', 'Kehadiran siswa berhasil disimpan dan ditandai sudah direview.');
    }

    public function rekapBulanan(): View
    {
        $this->authorize('viewAny', Attendance::class);

        $tahun = AcademicYear::active();
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $classGroup = request('class_group_id')
            ? $classes->firstWhere('id', (int) request('class_group_id'))
            : $classes->first();

        $month = request('month') ? Carbon::parse(request('month').'-01') : Carbon::now()->startOfMonth();

        $recap = $classGroup
            ? $this->attendance->monthlyRecap($tahun, $classGroup, $month)
            : [
                'rows' => collect(),
                'reviewed_count' => 0,
                'summary' => [
                    'total_sakit' => 0,
                    'total_izin' => 0,
                    'total_alfa' => 0,
                    'total_ketidakhadiran' => 0,
                    'persentase_ketidakhadiran' => null,
                    'persentase_kehadiran' => null,
                ],
                'month' => $month,
                'days_in_month' => $month->daysInMonth,
            ];

        return view('pages.kesiswaan.kehadiran.rekap-bulanan', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Kehadiran Siswa', 'href' => route('kehadiran.index')],
                ['label' => 'Rekap Bulanan'],
            ],
            'tahun' => $tahun,
            'classGroup' => $classGroup,
            'classes' => $classes,
            'month' => $recap['month'],
            'daysInMonth' => $recap['days_in_month'],
            'reviewedCount' => $recap['reviewed_count'],
            'rows' => $recap['rows'],
            'summary' => $recap['summary'],
        ]);
    }

    protected function assertDateEditable(Carbon $tanggal): void
    {
        abort_unless(
            $this->attendance->isDateEditable(auth()->user(), $tanggal),
            403,
            'Kehadiran tanggal ini terkunci. Hubungi Kepala Madrasah / Wakamad untuk membuka.',
        );
    }
}
