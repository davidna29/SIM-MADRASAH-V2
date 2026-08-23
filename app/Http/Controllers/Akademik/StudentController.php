<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Student::class);

        $tahun = AcademicYear::active();

        $students = Student::with(['person', 'enrollments' => fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif')->with('classGroup')])
            ->when(request('q'), fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('nis', 'like', "%{$search}%")
                    ->orWhereHas('person', fn ($p) => $p->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"));
            }))
            ->when(request('class_group_id'), fn ($q, $id) => $q->whereHas('enrollments', fn ($e) => $e->where('class_group_id', $id)->where('academic_year_id', $tahun->id)))
            ->when(request('grade_level'), fn ($q, $level) => $q->whereHas('enrollments', fn ($e) => $e->whereHas('classGroup', fn ($c) => $c->where('grade_level', $level))->where('academic_year_id', $tahun->id)))
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.siswa.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Data Siswa'],
            ],
            'students' => $students,
            'tahun' => $tahun,
            'gradeOptions' => ['I' => 'I', 'II' => 'II', 'III' => 'III', 'IV' => 'IV', 'V' => 'V', 'VI' => 'VI'],
            'classOptions' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Student::class);

        return view('pages.siswa.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => 'Tambah Siswa'],
            ],
            'editing' => false,
            'tahun' => AcademicYear::active(),
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->authorize('create', Student::class);

        $validated = $request->validated();

        $student = DB::transaction(function () use ($validated) {
            $person = Person::create([
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'] ?? 'Islam',
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            $student = Student::create([
                'person_id' => $person->id,
                'nis' => $validated['nis'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
            ]);

            if (! empty($validated['class_group_id'])) {
                StudentEnrollment::create([
                    'academic_year_id' => AcademicYear::active()->id,
                    'class_group_id' => $validated['class_group_id'],
                    'student_id' => $student->id,
                    'status' => 'aktif',
                ]);
            }

            return $student;
        });

        activity('akademik')->performedOn($student)->log('siswa_baru');

        return redirect()->route('siswa.show', $student)->with('status', 'Data siswa berhasil disimpan dan disematkan ke papan.');
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $tahun = AcademicYear::active();

        return view('pages.siswa.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => $student->displayName()],
            ],
            'student' => $student->load(['person', 'enrollments.classGroup', 'guardians']),
            'tahun' => $tahun,
        ]);
    }

    public function edit(Student $student): View
    {
        $this->authorize('update', $student);

        return view('pages.siswa.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => 'Ubah '.$student->displayName()],
            ],
            'editing' => true,
            'student' => $student->load(['person', 'enrollments']),
            'tahun' => AcademicYear::active(),
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $student) {
            $student->person->update([
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'] ?? 'Islam',
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            $student->update([
                'nis' => $validated['nis'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
            ]);

            // Penempatan aktif diperbarui (tahun berjalan)
            if (! empty($validated['class_group_id'])) {
                StudentEnrollment::updateOrCreate(
                    [
                        'academic_year_id' => AcademicYear::active()->id,
                        'student_id' => $student->id,
                    ],
                    ['class_group_id' => $validated['class_group_id'], 'status' => 'aktif']
                );
            }
        });

        activity('akademik')->performedOn($student)->log('siswa_diubah');

        return redirect()->route('siswa.show', $student)->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);

        if ($student->enrollments()->where('status', 'aktif')->exists()) {
            return back()->withErrors(['delete' => "Siswa {$student->displayName()} masih aktif pada tahun berjalan dan tidak dapat dihapus."]);
        }

        $student->delete();

        activity('akademik')->performedOn($student)->log('siswa_dihapus');

        return redirect()->route('siswa.index')->with('status', 'Data siswa dihapus.');
    }
}
