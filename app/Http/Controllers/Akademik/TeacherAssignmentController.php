<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherAssignmentRequest;
use App\Http\Requests\UpdateTeacherAssignmentRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherAssignmentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TeacherAssignment::class);

        $tahun = AcademicYear::active();

        $assignments = TeacherAssignment::with(['classGroup', 'subject', 'teacher'])
            ->when(request('grade_level'), fn ($q, $level) => $q->whereHas('classGroup', fn ($c) => $c->where('grade_level', $level)))
            ->when(request('subject_id'), fn ($q, $id) => $q->where('subject_id', $id))
            ->where('academic_year_id', $tahun->id)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.penugasan.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Penugasan Mengajar'],
            ],
            'assignments' => $assignments,
            'tahun' => $tahun,
            'gradeOptions' => ['VII' => 'VII', 'VIII' => 'VIII', 'IX' => 'IX'],
            'subjectOptions' => Subject::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', TeacherAssignment::class);

        return view('pages.penugasan.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Penugasan Mengajar', 'href' => route('penugasan.index')],
                ['label' => 'Tambah Penugasan'],
            ],
            'editing' => false,
            'tahun' => AcademicYear::active(),
            'teachers' => $this->teacherOptions(),
            'classes' => ClassGroup::orderBy('grade_level')->orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function store(StoreTeacherAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', TeacherAssignment::class);

        try {
            $assignment = DB::transaction(function () use ($request) {
                return TeacherAssignment::create($request->validated());
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withInput()->withErrors([
                    'class_group_id' => 'Guru sudah ditugaskan untuk kelas & mapel ini pada tahun ajaran tersebut.',
                ]);
            }

            throw $e;
        }

        activity('akademik')->performedOn($assignment)->log('penugasan_baru');

        return redirect()->route('penugasan.index')->with('status', 'Penugasan mengajar berhasil disimpan dan disematkan ke papan.');
    }

    public function edit(TeacherAssignment $assignment): View
    {
        $this->authorize('update', $assignment);

        return view('pages.penugasan.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Penugasan Mengajar', 'href' => route('penugasan.index')],
                ['label' => 'Ubah Penugasan'],
            ],
            'editing' => true,
            'assignment' => $assignment->load(['classGroup', 'subject', 'teacher']),
            'tahun' => AcademicYear::active(),
            'teachers' => $this->teacherOptions(),
            'classes' => ClassGroup::orderBy('grade_level')->orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTeacherAssignmentRequest $request, TeacherAssignment $assignment): RedirectResponse
    {
        $this->authorize('update', $assignment);

        $assignment->update($request->validated());

        activity('akademik')->performedOn($assignment)->log('penugasan_diubah');

        return redirect()->route('penugasan.index')->with('status', 'Penugasan mengajar berhasil diperbarui.');
    }

    public function destroy(TeacherAssignment $assignment): RedirectResponse
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        activity('akademik')->performedOn($assignment)->log('penugasan_dihapus');

        return redirect()->route('penugasan.index')->with('status', 'Penugasan mengajar dihapus.');
    }

    protected function teacherOptions()
    {
        return User::where('role', 'guru')->orderBy('name')->pluck('name', 'id');
    }
}
