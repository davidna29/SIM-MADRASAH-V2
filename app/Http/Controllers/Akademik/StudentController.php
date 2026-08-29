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
            // Siswa yang sudah mutasi keluar tidak tampil di daftar aktif.
            ->whereDoesntHave('enrollments', fn ($e) => $e->where('academic_year_id', $tahun->id)->where('status', 'keluar'))
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
            'student' => $student->load(['person', 'enrollments.classGroup', 'guardians', 'ppdbRegistration']),
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
            'student' => $student->load(['person', 'enrollments', 'guardians']),
            'tahun' => AcademicYear::active(),
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $student, $request) {
            $student->person->update([
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'] ?? 'Islam',
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'district' => $validated['district'] ?? null,
                'village' => $validated['village'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'home_phone' => $validated['home_phone'] ?? null,
            ]);

            $student->update([
                'nis' => $validated['nis'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'nisn' => $validated['nisn'] ?? null,
                'previous_school' => $validated['previous_school'] ?? null,
                'origin_school' => $validated['origin_school'] ?? null,
                'origin_nsm' => $validated['origin_nsm'] ?? null,
                'origin_npsn' => $validated['origin_npsn'] ?? null,
                'origin_address' => $validated['origin_address'] ?? null,
                'entry_date' => $validated['entry_date'] ?? null,
                'hobby' => $validated['hobby'] ?? null,
                'ambition' => $validated['ambition'] ?? null,
                'child_order' => $validated['child_order'] ?? null,
                'sibling_count' => $validated['sibling_count'] ?? null,
                'ever_tk' => $validated['ever_tk'] ?? null,
                'ever_paud' => $validated['ever_paud'] ?? null,
                'residence_type' => $validated['residence_type'] ?? null,
                'distance' => $validated['distance'] ?? null,
                'transport' => $validated['transport'] ?? null,
                'commute_time' => $validated['commute_time'] ?? null,
                'kk_number' => $validated['kk_number'] ?? null,
                'kk_head_name' => $validated['kk_head_name'] ?? null,
                'social_kks' => $validated['social_kks'] ?? null,
                'social_pkh' => $validated['social_pkh'] ?? null,
                'social_kip' => $validated['social_kip'] ?? null,
                'parent_ownership' => $validated['parent_ownership'] ?? null,
                'parent_address' => $validated['parent_address'] ?? null,
                'parent_province' => $validated['parent_province'] ?? null,
                'parent_city' => $validated['parent_city'] ?? null,
                'parent_district' => $validated['parent_district'] ?? null,
                'parent_village' => $validated['parent_village'] ?? null,
                'parent_rt' => $validated['parent_rt'] ?? null,
                'parent_rw' => $validated['parent_rw'] ?? null,
                'parent_postal_code' => $validated['parent_postal_code'] ?? null,
                'imm_hepb' => $request->boolean('imm_hepb'),
                'imm_polio' => $request->boolean('imm_polio'),
                'imm_bcg' => $request->boolean('imm_bcg'),
                'imm_campak' => $request->boolean('imm_campak'),
                'imm_dpt' => $request->boolean('imm_dpt'),
                'imm_covid' => $request->boolean('imm_covid'),
                'dis_deaf' => $request->boolean('dis_deaf'),
                'dis_blind' => $request->boolean('dis_blind'),
                'dis_disabled' => $request->boolean('dis_disabled'),
                'dis_intellectual' => $request->boolean('dis_intellectual'),
                'dis_behavioral' => $request->boolean('dis_behavioral'),
                'dis_slow_learner' => $request->boolean('dis_slow_learner'),
                'dis_communication' => $request->boolean('dis_communication'),
                'dis_gifted' => $request->boolean('dis_gifted'),
                'documents' => array_filter([
                    'kk' => $validated['scanned_kk'] ?? null,
                    'kk_wali' => $validated['scanned_kk_wali'] ?? null,
                    'akta' => $validated['scanned_akta'] ?? null,
                    'ijazah' => $validated['scanned_ijazah'] ?? null,
                    'photo' => $validated['scanned_photo'] ?? null,
                ]),
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

            $this->upsertGuardians($student, $validated);
        });

        activity('akademik')->performedOn($student)->log('siswa_diubah');

        return redirect()->route('siswa.show', $student)->with('status', 'Data siswa berhasil diperbarui.');
    }

    protected function upsertGuardians(Student $student, array $data): void
    {
        $people = [
            'ayah' => ['prefix' => 'father', 'relation' => 'ayah'],
            'ibu' => ['prefix' => 'mother', 'relation' => 'ibu'],
            'wali' => ['prefix' => 'guardian', 'relation' => 'wali'],
        ];

        foreach ($people as $cfg) {
            $p = $cfg['prefix'];
            $name = $data[$p.'_name'] ?? null;

            if (blank($name)) {
                continue; // kosong = biarkan apa adanya (tidak menghapus)
            }

            $fields = [
                'name' => $name,
                'nik' => $data[$p.'_nik'] ?? null,
                'status' => $data[$p.'_status'] ?? null,
                'birth_place' => $data[$p.'_birth_place'] ?? null,
                'birth_date' => $data[$p.'_birth_date'] ?? null,
                'education' => $data[$p.'_education'] ?? null,
                'job' => $data[$p.'_job'] ?? null,
                'income' => $data[$p.'_income'] ?? null,
                'phone' => $data[$p.'_phone'] ?? null,
            ];

            $guardianId = $data[$p.'_id'] ?? null;

            if ($guardianId && $guardian = Guardian::find($guardianId)) {
                $guardian->update($fields);

                continue;
            }

            $guardian = Guardian::create(['user_id' => null] + $fields);

            if (! $student->guardians()->whereKey($guardian->id)->exists()) {
                $student->guardians()->attach($guardian->id, ['relation' => $cfg['relation']]);
            }
        }
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
