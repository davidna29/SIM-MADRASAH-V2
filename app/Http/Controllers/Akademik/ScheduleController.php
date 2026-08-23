<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Schedule;
use App\Models\TeacherAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    protected array $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    public function index(): View
    {
        $this->authorize('viewAny', Schedule::class);

        $tahun = AcademicYear::active();
        $classGroupId = request('class_group_id');

        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $schedules = collect();
        if ($classGroupId) {
            $schedules = Schedule::with(['assignment.subject', 'assignment.teacher'])
                ->where('academic_year_id', $tahun->id)
                ->whereHas('assignment', fn ($q) => $q->where('class_group_id', $classGroupId))
                ->orderByRaw("FIELD(day,'senin','selasa','rabu','kamis','jumat','sabtu')")
                ->orderBy('start_time')
                ->get();
        }

        return view('pages.jadwal.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Mengajar'],
            ],
            'tahun' => $tahun,
            'classes' => $classes,
            'selectedClass' => $classGroupId ? ClassGroup::find($classGroupId) : null,
            'schedules' => $schedules,
            'days' => $this->days,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Schedule::class);

        return view('pages.jadwal.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Mengajar', 'href' => route('jadwal.index')],
                ['label' => 'Tambah Jadwal'],
            ],
            'editing' => false,
            'assignments' => $this->assignmentOptions(),
            'days' => $this->days,
        ]);
    }

    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        $this->authorize('create', Schedule::class);

        $schedule = Schedule::create($request->validated() + [
            'academic_year_id' => AcademicYear::active()->id,
            'recorded_by' => auth()->id(),
        ]);

        activity('akademik')->performedOn($schedule)->log('jadwal_baru');

        return redirect()->route('jadwal.index')->with('status', 'Jadwal mengajar berhasil disimpan dan disematkan ke papan.');
    }

    public function edit(Schedule $schedule): View
    {
        $this->authorize('update', $schedule);

        return view('pages.jadwal.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Mengajar', 'href' => route('jadwal.index')],
                ['label' => 'Ubah Jadwal'],
            ],
            'editing' => true,
            'schedule' => $schedule->load('assignment'),
            'assignments' => $this->assignmentOptions(),
            'days' => $this->days,
        ]);
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule);

        $schedule->update($request->validated());

        activity('akademik')->performedOn($schedule)->log('jadwal_diubah');

        return redirect()->route('jadwal.index')->with('status', 'Jadwal mengajar berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        activity('akademik')->performedOn($schedule)->log('jadwal_dihapus');

        return redirect()->route('jadwal.index')->with('status', 'Jadwal mengajar dihapus.');
    }

    protected function assignmentOptions()
    {
        return TeacherAssignment::with(['subject', 'classGroup', 'teacher'])
            ->where('academic_year_id', AcademicYear::active()->id)
            ->get()
            ->mapWithKeys(fn ($a) => [
                $a->id => $a->subject->name.' — '.$a->classGroup->name.' ('.$a->teacher->name.')',
            ]);
    }
}
