<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleCellsRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\ScheduleCell;
use App\Models\ScheduleModel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleCellController extends Controller
{
    protected array $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    public function penyusunan(?ScheduleModel $model = null): View
    {
        $this->authorize('viewAny', ScheduleModel::class);

        $tahun = AcademicYear::active();

        if (! $model || $model->academic_year_id !== $tahun->id) {
            $model = ScheduleModel::where('academic_year_id', $tahun->id)->where('is_active', true)->first()
                ?? ScheduleModel::where('academic_year_id', $tahun->id)->first();
        }

        $models = ScheduleModel::where('academic_year_id', $tahun->id)->orderBy('name')->get();

        if ($model) {
            $model->load(['gradeLevelRows', 'slots', 'cells']);
        }

        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('pages.jadwal.penyusunan', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Penyusunan Jadwal'],
            ],
            'tahun' => $tahun,
            'models' => $models,
            'model' => $model,
            'days' => $this->days,
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }

    public function perKelas(ClassGroup $classGroup): View
    {
        $this->authorize('viewAny', ScheduleModel::class);

        $tahun = AcademicYear::active();

        $cells = ScheduleCell::with(['subject', 'teacher'])
            ->where('academic_year_id', $tahun->id)
            ->where('class_group_id', $classGroup->id)
            ->get();

        return view('pages.jadwal.per-kelas', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Jadwal Kelas '.$classGroup->name],
            ],
            'classGroup' => $classGroup,
            'days' => $this->days,
            'cells' => $cells,
            'maxPeriod' => $cells->max('period_no') ?? 8,
        ]);
    }

    public function perGuru(User $teacher): View
    {
        $this->authorize('viewAny', ScheduleModel::class);

        abort_unless($teacher->role === 'guru', 404);

        $tahun = AcademicYear::active();

        $cells = ScheduleCell::with(['subject', 'classGroup'])
            ->where('academic_year_id', $tahun->id)
            ->where('teacher_id', $teacher->id)
            ->get();

        return view('pages.jadwal.per-guru', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Jadwal Guru '.$teacher->name],
            ],
            'teacher' => $teacher,
            'days' => $this->days,
            'cells' => $cells,
        ]);
    }

    public function store(StoreScheduleCellsRequest $request, ScheduleModel $model): RedirectResponse
    {
        $this->authorize('update', $model);

        $tahun = AcademicYear::active();

        $validated = $request->validated();

        // Validasi konflik guru — hard-block
        $conflicts = $this->findTeacherConflicts($validated['cells']);

        if ($conflicts->isNotEmpty()) {
            return back()->withErrors([
                'cells' => 'Konflik guru ditemukan — perbaiki sebelum menyimpan: '.$conflicts->implode('; ').'.',
            ]);
        }

        DB::transaction(function () use ($validated, $model, $tahun) {
            foreach ($validated['cells'] as $cell) {
                $filled = ! empty($cell['teacher_id']) || ! empty($cell['subject_id']);

                if (! $filled) {
                    // Sel dikosongkan
                    ScheduleCell::where('schedule_model_id', $model->id)
                        ->where('academic_year_id', $tahun->id)
                        ->where('class_group_id', $cell['class_group_id'])
                        ->where('day', $cell['day'])
                        ->where('period_no', $cell['period_no'])
                        ->delete();
                    continue;
                }

                ScheduleCell::updateOrCreate(
                    [
                        'schedule_model_id' => $model->id,
                        'academic_year_id' => $tahun->id,
                        'class_group_id' => $cell['class_group_id'],
                        'day' => $cell['day'],
                        'period_no' => $cell['period_no'],
                    ],
                    [
                        'teacher_id' => $cell['teacher_id'] ?: null,
                        'subject_id' => $cell['subject_id'] ?: null,
                    ]
                );
            }
        });

        activity('akademik')->performedOn($model)->log('jadwal_disusun');

        return redirect()->route('jadwal.penyusunan', ['model' => $model->id])->with('status', 'Jadwal berhasil disimpan dan disematkan ke papan.');
    }

    public function generate(Request $request, ScheduleModel $model): RedirectResponse
    {
        $this->authorize('update', $model);

        $validated = $request->validate([
            'mode' => ['required', 'in:blank,copy'],
            'source_academic_year_id' => ['nullable', 'required_if:mode,copy', 'exists:academic_years,id'],
        ]);

        $tahun = AcademicYear::active();
        $tahunId = $tahun->id;
        $slots = $model->slots;

        // Proteksi: jangan menimpa data tahun tujuan yang sudah ada
        $existing = ScheduleCell::where('schedule_model_id', $model->id)
            ->where('academic_year_id', $tahunId)
            ->exists();

        if ($existing) {
            return back()->withErrors(['generate' => 'Tahun ajaran tujuan sudah memiliki data jadwal. Hapus data lama terlebih dahulu, atau pilih tahun lain.']);
        }

        $rombel = ClassGroup::whereIn('grade_level', $model->gradeLevels())->get();

        $sourceCells = collect();
        if ($validated['mode'] === 'copy') {
            $sourceCells = ScheduleCell::where('schedule_model_id', $model->id)
                ->where('academic_year_id', $validated['source_academic_year_id'])
                ->get()
                ->keyBy(fn ($c) => $c->class_group_id.'|'.$c->day.'|'.$c->period_no);
        }

        DB::transaction(function () use ($model, $tahunId, $slots, $rombel, $sourceCells, $validated) {
            foreach ($rombel as $class) {
                foreach ($this->days as $day) {
                    foreach ($slots as $slot) {
                        if ($slot->is_break) {
                            continue;
                        }

                        $source = $sourceCells->get($class->id.'|'.$day.'|'.$slot->period_no);

                        ScheduleCell::create([
                            'schedule_model_id' => $model->id,
                            'academic_year_id' => $tahunId,
                            'class_group_id' => $class->id,
                            'day' => $day,
                            'period_no' => $slot->period_no,
                            'teacher_id' => $source?->teacher_id,
                            'subject_id' => $source?->subject_id,
                        ]);
                    }
                }
            }
        });

        $mode = $validated['mode'] === 'blank' ? 'kerangka kosong' : 'salinan dari tahun sumber';
        activity('akademik')->performedOn($model)->log('jadwal_generate_'.$validated['mode']);

        return redirect()->route('jadwal.penyusunan', ['model' => $model->id])->with('status', 'Jadwal berhasil di-generate ('.$mode.').');
    }

    public function cetakKelas(ClassGroup $classGroup)
    {
        $this->authorize('viewAny', ScheduleModel::class);

        $tahun = AcademicYear::active();

        $cells = ScheduleCell::with(['subject', 'teacher'])
            ->where('academic_year_id', $tahun->id)
            ->where('class_group_id', $classGroup->id)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.jadwal-kelas', [
            'classGroup' => $classGroup,
            'tahun' => $tahun,
            'days' => $this->days,
            'cells' => $cells,
        ]);

        $tahunName = str_replace('/', '-', $tahun->name);

        return $pdf->download('jadwal-'.$classGroup->name.'-'.$tahunName.'.pdf');
    }

    public function cetakGuru(User $teacher)
    {
        $this->authorize('viewAny', ScheduleModel::class);

        abort_unless($teacher->role === 'guru', 404);

        $tahun = AcademicYear::active();

        $cells = ScheduleCell::with(['subject', 'classGroup'])
            ->where('academic_year_id', $tahun->id)
            ->where('teacher_id', $teacher->id)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.jadwal-guru', [
            'teacher' => $teacher,
            'tahun' => $tahun,
            'days' => $this->days,
            'cells' => $cells,
        ]);

        $tahunName = str_replace('/', '-', $tahun->name);

        return $pdf->download('jadwal-guru-'.str_replace(' ', '-', $teacher->name).'-'.$tahunName.'.pdf');
    }

    protected function findTeacherConflicts(array $cells)
    {
        // Peta (teacher_id -> (day|period) -> class_group_id)
        $slots = [];
        $conflicts = collect();

        foreach ($cells as $cell) {
            if (empty($cell['teacher_id'])) {
                continue;
            }

            $key = $cell['teacher_id'].'|'.$cell['day'].'|'.$cell['period_no'];

            if (isset($slots[$key]) && $slots[$key] !== $cell['class_group_id']) {
                $teacherName = User::find($cell['teacher_id'])?->name ?? "Guru #{$cell['teacher_id']}";
                $otherClass = ClassGroup::find($slots[$key])?->name ?? '?';

                $conflicts->push("{$teacherName} bentrok: kelas {$otherClass} & kelas ".($cell['class_group_id'] ? ClassGroup::find($cell['class_group_id'])?->name : '?')." pada ".ucfirst($cell['day'])." jam ke-{$cell['period_no']}");
            }

            $slots[$key] = $cell['class_group_id'];
        }

        // Cek bentrok dengan sel yang sudah tersimpan di DB (di luar batch ini)
        $existing = ScheduleCell::whereIn('teacher_id', array_filter(array_column($cells, 'teacher_id')))
            ->get()
            ->groupBy(fn ($c) => $c->teacher_id.'|'.$c->day.'|'.$c->period_no);

        foreach ($cells as $cell) {
            if (empty($cell['teacher_id'])) {
                continue;
            }

            $key = $cell['teacher_id'].'|'.$cell['day'].'|'.$cell['period_no'];
            $existingCells = $existing->get($key, collect());

            foreach ($existingCells as $ec) {
                if ($ec->class_group_id !== $cell['class_group_id']) {
                    $teacherName = User::find($cell['teacher_id'])?->name ?? "Guru #{$cell['teacher_id']}";
                    $conflicts->push("{$teacherName} sudah mengajar di kelas {$ec->classGroup?->name} pada ".ucfirst($cell['day'])." jam ke-{$cell['period_no']}");
                }
            }
        }

        return $conflicts->unique();
    }
}
