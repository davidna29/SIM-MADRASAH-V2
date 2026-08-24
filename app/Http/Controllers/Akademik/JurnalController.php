<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeachingJournal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TeachingJournal::class);

        $tahun = AcademicYear::active();

        $journals = TeachingJournal::with(['assignment.classGroup', 'assignment.subject', 'assignment.teacher'])
            ->where('academic_year_id', $tahun->id)
            ->when(request('class_group_id'), fn ($q, $id) => $q->whereHas('assignment', fn ($a) => $a->where('class_group_id', $id)))
            ->when(request('subject_id'), fn ($q, $id) => $q->whereHas('assignment', fn ($a) => $a->where('subject_id', $id)))
            ->when(request('teacher_id'), fn ($q, $id) => $q->whereHas('assignment', fn ($a) => $a->where('user_id', $id)))
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->when(request('from'), fn ($q, $d) => $q->whereDate('journal_date', '>=', $d))
            ->when(request('to'), fn ($q, $d) => $q->whereDate('journal_date', '<=', $d))
            ->orderByDesc('journal_date')
            ->orderByDesc('period_no')
            ->paginate(15)
            ->withQueryString();

        return view('pages.jurnal.monitor', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jurnal Mengajar'],
            ],
            'tahun' => $tahun,
            'journals' => $journals,
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => User::where('role', 'guru')->orderBy('name')->get(),
        ]);
    }

    public function mingguan(): View
    {
        $this->authorize('viewAny', TeachingJournal::class);

        $tahun = AcademicYear::active();
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $class = $classes->firstWhere('id', (int) request('class_group_id')) ?? $classes->first();

        $weekStart = request('week_start')
            ? Carbon::parse(request('week_start'))->startOfWeek(Carbon::MONDAY)
            : Carbon::today()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(5); // Sabtu

        $lk = 0;
        $pr = 0;
        $days = [];

        if ($class) {
            $enrollments = StudentEnrollment::with('student')
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->get();

            $lk = $enrollments->where('student.gender', 'L')->count();
            $pr = $enrollments->where('student.gender', 'P')->count();

            $journals = TeachingJournal::with(['assignment.subject', 'assignment.teacher', 'recorder'])
                ->whereHas('assignment', fn ($q) => $q->where('class_group_id', $class->id))
                ->where('status', 'terisi')
                ->whereBetween('journal_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->orderBy('journal_date')
                ->orderBy('period_no')
                ->get()
                ->groupBy(fn ($j) => $j->journal_date->format('Y-m-d'));

            for ($i = 0; $i <= 5; $i++) {
                $date = $weekStart->copy()->addDays($i);
                $days[] = [
                    'date' => $date,
                    'entries' => $journals->get($date->toDateString(), collect()),
                ];
            }
        }

        return view('pages.jurnal.mingguan', [
            'roleLabel' => match (auth()->user()->role) {
                'guru' => 'Guru Mata Pelajaran',
                'tata_usaha' => 'Tata Usaha',
                'wakamad_kurikulum' => 'Wakamad Kurikulum',
                'kepala_madrasah' => 'Kepala Madrasah',
                default => 'Super Admin',
            },
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jurnal Mengajar', 'href' => route('jurnal.admin.index')],
                ['label' => 'Mingguan per Kelas'],
            ],
            'tahun' => $tahun,
            'classes' => $classes,
            'class' => $class,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'monthLabel' => ucfirst($weekStart->locale('id')->translatedFormat('F Y')),
            'weekRange' => $this->formatWeekRange($weekStart, $weekEnd),
            'lk' => $lk,
            'pr' => $pr,
            'days' => $days,
        ]);
    }

    protected function formatWeekRange(Carbon $start, Carbon $end): string
    {
        $awal = $start->locale('id');
        $akhir = $end->locale('id');

        if ($start->month === $end->month) {
            return $awal->translatedFormat('j').'–'.$akhir->translatedFormat('j M Y');
        }

        return $awal->translatedFormat('j M').' – '.$akhir->translatedFormat('j M Y');
    }

    public function mingguanGuru(): View
    {
        $this->authorize('viewAny', TeachingJournal::class);

        $tahun = AcademicYear::active();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();

        $teacher = $teachers->firstWhere('id', (int) request('teacher_id')) ?? $teachers->first();

        $weekStart = request('week_start')
            ? Carbon::parse(request('week_start'))->startOfWeek(Carbon::MONDAY)
            : Carbon::today()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(5); // Sabtu

        $rombelCount = 0;
        $mapelCount = 0;
        $days = [];

        if ($teacher) {
            $assignments = $teacher->assignments()->where('academic_year_id', $tahun->id)->get();

            $rombelCount = $assignments->pluck('class_group_id')->unique()->count();
            $mapelCount = $assignments->pluck('subject_id')->unique()->count();

            $journals = TeachingJournal::with(['assignment.classGroup', 'assignment.subject', 'recorder'])
                ->whereHas('assignment', fn ($q) => $q->where('user_id', $teacher->id))
                ->where('status', 'terisi')
                ->whereBetween('journal_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->orderBy('journal_date')
                ->orderBy('period_no')
                ->get()
                ->groupBy(fn ($j) => $j->journal_date->format('Y-m-d'));

            for ($i = 0; $i <= 5; $i++) {
                $date = $weekStart->copy()->addDays($i);
                $days[] = [
                    'date' => $date,
                    'entries' => $journals->get($date->toDateString(), collect()),
                ];
            }
        }

        return view('pages.jurnal.mingguan-guru', [
            'roleLabel' => match (auth()->user()->role) {
                'guru' => 'Guru Mata Pelajaran',
                'tata_usaha' => 'Tata Usaha',
                'wakamad_kurikulum' => 'Wakamad Kurikulum',
                'kepala_madrasah' => 'Kepala Madrasah',
                default => 'Super Admin',
            },
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jurnal Mengajar', 'href' => route('jurnal.admin.index')],
                ['label' => 'Mingguan per Guru'],
            ],
            'tahun' => $tahun,
            'teachers' => $teachers,
            'teacher' => $teacher,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'monthLabel' => ucfirst($weekStart->locale('id')->translatedFormat('F Y')),
            'weekRange' => $this->formatWeekRange($weekStart, $weekEnd),
            'rombelCount' => $rombelCount,
            'mapelCount' => $mapelCount,
            'days' => $days,
        ]);
    }
}
