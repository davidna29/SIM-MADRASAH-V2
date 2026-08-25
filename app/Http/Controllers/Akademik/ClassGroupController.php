<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassGroupRequest;
use App\Http\Requests\UpdateClassGroupRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassGroupController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ClassGroup::class);

        $tahun = AcademicYear::active();

        $classes = ClassGroup::query()
            ->when(request('grade_level'), fn ($q, $level) => $q->where('grade_level', $level))
            ->withCount(['enrollments' => fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif')])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('pages.kelas.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Kelas & Penempatan'],
            ],
            'classes' => $classes,
            'tahun' => $tahun,
        ]);
    }

    public function show(ClassGroup $classGroup): View
    {
        $this->authorize('view', $classGroup);

        $tahun = AcademicYear::active();

        $enrollments = StudentEnrollment::with('student')
            ->where('academic_year_id', $tahun->id)
            ->where('class_group_id', $classGroup->id)
            ->where('status', 'aktif')
            ->orderBy('student_id')
            ->get();

        // Siswa yang sudah aktif di rombel lain (TA berjalan) tidak ditampilkan
        // pada daftar tersedia — hindari menempatkan ganda.
        $placedElsewhere = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->where('class_group_id', '!=', $classGroup->id)
            ->pluck('student_id');

        $availableStudents = Student::with('person')
            ->whereNotIn('id', $enrollments->pluck('student_id'))
            ->whereNotIn('id', $placedElsewhere)
            ->when(request('q'), fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('nis', 'like', "%{$search}%")
                    ->orWhereHas('person', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            }))
            ->orderBy('name')
            ->get();

        return view('pages.kelas.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Kelas & Penempatan', 'href' => route('kelas.index')],
                ['label' => $classGroup->name],
            ],
            'classGroup' => $classGroup->load('homeroom.teacher'),
            'tahun' => $tahun,
            'enrollments' => $enrollments,
            'availableStudents' => $availableStudents,
            'search' => request('q'),
            'teachers' => User::whereIn('role', ['guru', 'guru_bk'])->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ClassGroup::class);

        return view('pages.kelas.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Kelas & Penempatan', 'href' => route('kelas.index')],
                ['label' => 'Tambah Kelas'],
            ],
            'editing' => false,
        ]);
    }

    public function store(StoreClassGroupRequest $request): RedirectResponse
    {
        $this->authorize('create', ClassGroup::class);

        $classGroup = ClassGroup::create($request->validated());

        activity('akademik')->performedOn($classGroup)->log('kelas_baru');

        return redirect()->route('kelas.show', $classGroup)->with('status', 'Kelas berhasil dibuat.');
    }

    public function edit(ClassGroup $classGroup): View
    {
        $this->authorize('update', $classGroup);

        return view('pages.kelas.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Kelas & Penempatan', 'href' => route('kelas.index')],
                ['label' => 'Ubah '.$classGroup->name],
            ],
            'editing' => true,
            'classGroup' => $classGroup,
        ]);
    }

    public function update(UpdateClassGroupRequest $request, ClassGroup $classGroup): RedirectResponse
    {
        $this->authorize('update', $classGroup);

        $classGroup->update($request->validated());

        activity('akademik')->performedOn($classGroup)->log('kelas_diubah');

        return redirect()->route('kelas.show', $classGroup)->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ClassGroup $classGroup): RedirectResponse
    {
        $this->authorize('delete', $classGroup);

        if ($classGroup->enrollments()->where('status', 'aktif')->exists() || $classGroup->assignments()->exists()) {
            return back()->withErrors(['delete' => "Kelas {$classGroup->name} masih memiliki siswa aktif atau penugasan mengajar dan tidak dapat dihapus."]);
        }

        $classGroup->delete();

        activity('akademik')->performedOn($classGroup)->log('kelas_dihapus');

        return redirect()->route('kelas.index')->with('status', 'Kelas dihapus.');
    }

    public function place(ClassGroup $classGroup): RedirectResponse
    {
        $this->authorize('update', $classGroup);

        $tahun = AcademicYear::active();

        $studentIds = request()->validate(['student_ids' => ['nullable', 'array']])['student_ids'] ?? [];

        // Validasi siswa belum ditempatkan di kelas lain pada TA aktif
        $conflicts = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->whereIn('student_id', $studentIds)
            ->where('class_group_id', '!=', $classGroup->id)
            ->with('student')
            ->get();

        if ($conflicts->isNotEmpty()) {
            $names = $conflicts->map(fn ($e) => $e->student->name)->implode(', ');

            return back()->withErrors(['student_ids' => "Siswa sudah terdaftar di kelas lain: {$names}."]);
        }

        DB::transaction(function () use ($classGroup, $tahun, $studentIds) {
            foreach ($studentIds as $studentId) {
                StudentEnrollment::updateOrCreate(
                    [
                        'academic_year_id' => $tahun->id,
                        'class_group_id' => $classGroup->id,
                        'student_id' => $studentId,
                    ],
                    ['status' => 'aktif']
                );
            }
        });

        activity('akademik')->performedOn($classGroup)->log('penempatan_siswa');

        return back()->with('status', 'Penempatan siswa berhasil disimpan.');
    }

    public function unplace(ClassGroup $classGroup, StudentEnrollment $enrollment): RedirectResponse
    {
        $this->authorize('update', $classGroup);

        abort_unless($enrollment->class_group_id === $classGroup->id, 404);

        $enrollment->update(['status' => 'alumni']);

        activity('akademik')->performedOn($classGroup)->log('penghapusan_penempatan');

        return back()->with('status', 'Siswa dikeluarkan dari kelas (status alumni).');
    }
}
