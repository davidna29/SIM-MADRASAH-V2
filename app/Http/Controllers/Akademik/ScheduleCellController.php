<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\ScheduleCell;
use App\Models\ScheduleModel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
}
