<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleModelRequest;
use App\Http\Requests\UpdateScheduleModelRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\ScheduleModel;
use App\Models\ScheduleModelGradeLevel;
use App\Models\ScheduleSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleModelController extends Controller
{
    protected array $gradeOptions = ['I' => 'I', 'II' => 'II', 'III' => 'III', 'IV' => 'IV', 'V' => 'V', 'VI' => 'VI'];

    public function index(): View
    {
        $this->authorize('viewAny', ScheduleModel::class);

        $tahun = AcademicYear::active();

        $models = ScheduleModel::with(['academicYear', 'gradeLevelRows', 'slots'])
            ->where('academic_year_id', $tahun->id)
            ->orderByDesc('id')
            ->get();

        return view('pages.jadwal.model.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Model Jadwal'],
            ],
            'tahun' => $tahun,
            'models' => $models,
            'gradeOptions' => $this->gradeOptions,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ScheduleModel::class);

        return view('pages.jadwal.model.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Model Jadwal', 'href' => route('jadwal.model.index')],
                ['label' => 'Tambah Model'],
            ],
            'editing' => false,
            'tahun' => AcademicYear::active(),
            'gradeOptions' => $this->gradeOptions,
        ]);
    }

    public function store(StoreScheduleModelRequest $request): RedirectResponse
    {
        $this->authorize('create', ScheduleModel::class);

        $validated = $request->validated();

        $conflict = $this->activeConflict($validated['grade_levels'], $validated['academic_year_id'], null);

        if ($conflict) {
            return back()->withInput()->withErrors([
                'grade_levels' => "Tingkatan ini sudah dicakup model aktif lain: {$conflict}.",
            ]);
        }

        $isActive = $request->boolean('is_active', true);

        $model = DB::transaction(function () use ($validated, $isActive) {
            $model = ScheduleModel::create([
                'academic_year_id' => $validated['academic_year_id'],
                'name' => $validated['name'],
                'start_time' => $validated['start_time'],
                'max_hours_per_day' => $validated['max_hours_per_day'],
                'is_active' => $isActive,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['grade_levels'] as $level) {
                ScheduleModelGradeLevel::create([
                    'schedule_model_id' => $model->id,
                    'grade_level' => $level,
                ]);
            }

            // Generate slot KBM otomatis dari start_time + durasi
            $start = Carbon::parse($validated['start_time']);
            $duration = (int) $validated['slot_duration'];
            for ($i = 1; $i <= $validated['max_hours_per_day']; $i++) {
                ScheduleSlot::create([
                    'schedule_model_id' => $model->id,
                    'period_no' => $i,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $start->copy()->addMinutes($duration)->format('H:i'),
                    'is_break' => false,
                ]);
                $start->addMinutes($duration);
            }

            return $model;
        });

        activity('akademik')->performedOn($model)->log('model_jadwal_baru');

        return redirect()->route('jadwal.model.show', $model)->with('status', 'Model jadwal berhasil dibuat.');
    }

    public function show(ScheduleModel $model): View
    {
        $this->authorize('view', $model);

        return view('pages.jadwal.model.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Model Jadwal', 'href' => route('jadwal.model.index')],
                ['label' => $model->name],
            ],
            'model' => $model->load(['academicYear', 'gradeLevelRows', 'slots']),
            'classes' => ClassGroup::whereIn('grade_level', $model->gradeLevels())
                ->orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function edit(ScheduleModel $model): View
    {
        $this->authorize('update', $model);

        return view('pages.jadwal.model.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Jadwal Pelajaran', 'href' => route('jadwal.index')],
                ['label' => 'Model Jadwal', 'href' => route('jadwal.model.index')],
                ['label' => 'Ubah '.$model->name],
            ],
            'editing' => true,
            'model' => $model->load(['academicYear', 'gradeLevelRows']),
            'tahun' => $model->academicYear,
            'gradeOptions' => $this->gradeOptions,
        ]);
    }

    public function update(UpdateScheduleModelRequest $request, ScheduleModel $model): RedirectResponse
    {
        $this->authorize('update', $model);

        $validated = $request->validated();

        $conflict = $this->activeConflict($validated['grade_levels'], $model->academic_year_id, $model->id);

        if ($conflict) {
            return back()->withInput()->withErrors([
                'grade_levels' => "Tingkatan ini sudah dicakup model aktif lain: {$conflict}.",
            ]);
        }

        DB::transaction(function () use ($validated, $model, $request) {
            $model->update([
                'name' => $validated['name'],
                'start_time' => $validated['start_time'],
                'max_hours_per_day' => $validated['max_hours_per_day'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            $model->gradeLevelRows()->delete();
            foreach ($validated['grade_levels'] as $level) {
                ScheduleModelGradeLevel::create([
                    'schedule_model_id' => $model->id,
                    'grade_level' => $level,
                ]);
            }
        });

        activity('akademik')->performedOn($model)->log('model_jadwal_diubah');

        return redirect()->route('jadwal.model.show', $model)->with('status', 'Model jadwal berhasil diperbarui.');
    }

    public function destroy(ScheduleModel $model): RedirectResponse
    {
        $this->authorize('delete', $model);

        $model->delete();

        activity('akademik')->performedOn($model)->log('model_jadwal_dihapus');

        return redirect()->route('jadwal.model.index')->with('status', 'Model jadwal dihapus.');
    }

    protected function activeConflict(array $gradeLevels, int $yearId, ?int $exceptId): ?string
    {
        $others = ScheduleModel::with('gradeLevelRows')
            ->where('academic_year_id', $yearId)
            ->where('is_active', true)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->get();

        $overlap = collect();
        foreach ($others as $other) {
            $otherLevels = $other->gradeLevels();
            $hit = array_intersect($gradeLevels, $otherLevels);
            if ($hit) {
                $overlap->push($other->name.' ('.implode(', ', $hit).')');
            }
        }

        return $overlap->isEmpty() ? null : $overlap->implode('; ');
    }
}
