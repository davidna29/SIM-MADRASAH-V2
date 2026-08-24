<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceReview;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    protected array $privilegedRoles = ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan'];

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
            'editable' => $date->isToday() || in_array(auth()->user()->role, $this->privilegedRoles, true),
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

        $tahun = AcademicYear::active();
        $classGroupId = null;

        foreach ($validated['attendances'] as $enrollmentId => $data) {
            $status = $data['status'] ?? 'hadir';
            $note = $data['note'] ?? null;

            $enrollment = StudentEnrollment::find($enrollmentId);

            if (! $enrollment) {
                continue;
            }

            $classGroupId ??= $enrollment->class_group_id;

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

        // Hari ini dianggap sudah direview begitu "Simpan Kehadiran" ditekan (unique per kelas+tanggal)
        if ($classGroupId) {
            AttendanceReview::updateOrCreate(
                [
                    'class_group_id' => $classGroupId,
                    'attendance_date' => $tanggal->toDateString(),
                ],
                [
                    'academic_year_id' => $tahun->id,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]
            );
        }

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
        $daysInMonth = $month->daysInMonth;

        $rows = collect();
        $reviewedCount = 0;
        $summary = [
            'total_sakit' => 0,
            'total_izin' => 0,
            'total_alfa' => 0,
            'total_ketidakhadiran' => 0,
            'persentase_ketidakhadiran' => null,
            'persentase_kehadiran' => null,
        ];

        if ($classGroup) {
            $enrollments = StudentEnrollment::with('student')
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $classGroup->id)
                ->where('status', 'aktif')
                ->orderBy('student_id')
                ->get();

            $start = $month->copy()->startOfMonth()->toDateString();
            $end = $month->copy()->endOfMonth()->toDateString();

            $reviewedDates = AttendanceReview::where('class_group_id', $classGroup->id)
                ->whereBetween('attendance_date', [$start, $end])
                ->pluck('attendance_date')
                ->map(fn ($d) => $d->format('Y-m-d'))
                ->flip();

            $reviewedCount = $reviewedDates->count();

            $attendances = Attendance::where('class_group_id', $classGroup->id)
                ->whereBetween('attendance_date', [$start, $end])
                ->get()
                ->groupBy(fn ($a) => $a->student_enrollment_id.'|'.$a->attendance_date->format('Y-m-d'));

            $rows = $enrollments->map(function ($enrollment) use ($attendances, $reviewedDates, $month, $daysInMonth, $reviewedCount) {
                $cells = [];
                $tally = ['S' => 0, 'I' => 0, 'A' => 0, 'H' => 0];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateStr = $month->copy()->day($d)->format('Y-m-d');

                    // Hari yang belum direview tampil kosong — tidak dianggap Alpha
                    if (! isset($reviewedDates[$dateStr])) {
                        $cells[$d] = null;

                        continue;
                    }

                    $status = $attendances->get($enrollment->id.'|'.$dateStr)?->first()?->status;
                    $mark = match ($status) {
                        'hadir' => '•',
                        'sakit' => 'S',
                        'izin' => 'I',
                        'alpha' => 'A',
                        default => null,
                    };

                    $cells[$d] = $mark;
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

            $totalSiswa = $enrollments->count();
            $totalHadir = $rows->sum(fn ($r) => $r['tally']['H']);
            $totalSakit = $rows->sum(fn ($r) => $r['tally']['S']);
            $totalIzin = $rows->sum(fn ($r) => $r['tally']['I']);
            $totalAlfa = $rows->sum(fn ($r) => $r['tally']['A']);
            $totalKetidakhadiran = $totalSakit + $totalIzin + $totalAlfa;
            $totalSlot = $totalSiswa * $reviewedCount;

            $summary = [
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_alfa' => $totalAlfa,
                'total_ketidakhadiran' => $totalKetidakhadiran,
                'persentase_ketidakhadiran' => $totalSlot > 0 ? round($totalKetidakhadiran / $totalSlot * 100, 1) : null,
                'persentase_kehadiran' => $totalSlot > 0 ? round($totalHadir / $totalSlot * 100, 1) : null,
            ];
        }

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
            'month' => $month,
            'daysInMonth' => $daysInMonth,
            'reviewedCount' => $reviewedCount,
            'rows' => $rows,
            'summary' => $summary,
        ]);
    }

    protected function assertDateEditable(Carbon $tanggal): void
    {
        $isToday = $tanggal->isToday();
        $isPrivileged = in_array(auth()->user()->role, $this->privilegedRoles, true);

        abort_unless($isToday || $isPrivileged, 403,
            'Kehadiran tanggal ini terkunci. Hubungi Kepala Madrasah / Wakamad untuk membuka.');
    }
}
