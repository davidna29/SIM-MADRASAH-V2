<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExtracurricularController extends Controller
{
    protected array $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

    public function index(): View
    {
        $this->authorize('viewAny', Extracurricular::class);

        $ekskuls = Extracurricular::with('pembina')
            ->withCount('members')
            ->when(request('status'), fn ($q, $v) => $q->where('status', $v))
            ->when(request('q'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.kesiswaan.ekstrakurikuler.index', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Ekstrakurikuler'],
            ],
            'ekskuls' => $ekskuls,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Extracurricular::class);

        return view('pages.kesiswaan.ekstrakurikuler.form', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Ekstrakurikuler', 'href' => route('ekskul.index')],
                ['label' => 'Tambah Ekstrakurikuler'],
            ],
            'editing' => false,
            'pembinaOptions' => $this->pembinaOptions(),
            'hariList' => $this->hariList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Extracurricular::class);

        $validated = $request->validate($this->eksRules());

        $ekskul = Extracurricular::create([
            ...$validated,
            'slug' => $this->uniqueSlug($validated['name']),
            'created_by' => auth()->id(),
        ]);

        activity('kesiswaan')->performedOn($ekskul)->log('ekskul_dibuat');

        return redirect()->route('ekskul.show', $ekskul)->with('status', 'Ekstrakurikuler dibuat — silakan tambahkan anggota.');
    }

    public function show(Extracurricular $ekskul): View
    {
        $this->authorize('view', $ekskul);

        $tahun = AcademicYear::active();
        $tanggal = request('tanggal') ?: now()->toDateString();

        $members = $ekskul->members()
            ->with('enrollment.student', 'enrollment.classGroup')
            ->orderBy('id')
            ->get();

        // Presensi untuk tanggal terpilih (default hari ini)
        $presensi = $ekskul->attendances()
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('student_enrollment_id');

        // Rekap kehadiran & nilai per anggota
        $attByMember = $ekskul->attendances()->get()->groupBy('student_enrollment_id');
        $rekap = $members->map(function ($member) use ($attByMember) {
            $rows = $attByMember->get($member->student_enrollment_id, collect());
            $points = $rows->filter(fn ($r) => $r->predikat !== null)
                ->map(fn ($r) => ExtracurricularAttendance::POINTS[$r->predikat]);

            $rata = $points->isNotEmpty() ? round($points->avg(), 2) : null;

            return [
                'member' => $member,
                'H' => $rows->where('status', 'hadir')->count(),
                'I' => $rows->where('status', 'izin')->count(),
                'S' => $rows->where('status', 'sakit')->count(),
                'A' => $rows->where('status', 'alpha')->count(),
                'rata' => $rata,
                'predikat' => $rata !== null ? ExtracurricularAttendance::predicateFromAverage($rata) : null,
            ];
        });

        // Kandidat anggota: siswa aktif di kelas terpilih yang belum jadi anggota
        $classId = request('class_group_id') ? (int) request('class_group_id') : null;

        $anggotaIds = $members->pluck('student_enrollment_id');
        $candidates = collect();
        if ($classId) {
            $candidates = StudentEnrollment::with('student')
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $classId)
                ->where('status', 'aktif')
                ->whereNotIn('id', $anggotaIds)
                ->orderBy('student_id')
                ->get();
        }

        return view('pages.kesiswaan.ekstrakurikuler.show', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Ekstrakurikuler', 'href' => route('ekskul.index')],
                ['label' => $ekskul->name],
            ],
            'ekskul' => $ekskul,
            'canManage' => auth()->user()->can('update', $ekskul),
            'tanggal' => $tanggal,
            'presensi' => $presensi,
            'rekap' => $rekap,
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
            'selectedClassId' => $classId,
            'candidates' => $candidates,
        ]);
    }

    public function edit(Extracurricular $ekskul): View
    {
        $this->authorize('update', $ekskul);

        return view('pages.kesiswaan.ekstrakurikuler.form', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Ekstrakurikuler', 'href' => route('ekskul.index')],
                ['label' => 'Ubah '.$ekskul->name],
            ],
            'editing' => true,
            'ekskul' => $ekskul,
            'pembinaOptions' => $this->pembinaOptions(),
            'hariList' => $this->hariList,
        ]);
    }

    public function update(Request $request, Extracurricular $ekskul): RedirectResponse
    {
        $this->authorize('update', $ekskul);

        $validated = $request->validate($this->eksRules());

        $ekskul->update($validated);

        activity('kesiswaan')->performedOn($ekskul)->log('ekskul_diubah');

        return redirect()->route('ekskul.show', $ekskul)->with('status', 'Ekstrakurikuler diperbarui.');
    }

    public function destroy(Extracurricular $ekskul): RedirectResponse
    {
        $this->authorize('delete', $ekskul);

        $ekskul->delete();

        activity('kesiswaan')->performedOn($ekskul)->log('ekskul_dihapus');

        return redirect()->route('ekskul.index')->with('status', 'Ekstrakurikuler dihapus beserta anggota & presensinya.');
    }

    public function memberStore(Request $request, Extracurricular $ekskul): RedirectResponse
    {
        $this->authorize('update', $ekskul);

        $validated = $request->validate([
            'student_enrollment_id' => ['required', 'exists:student_enrollments,id'],
            'tanggal_bergabung' => ['nullable', 'date'],
        ]);

        $enrollment = StudentEnrollment::where('id', $validated['student_enrollment_id'])
            ->where('academic_year_id', AcademicYear::active()->id)
            ->where('status', 'aktif')
            ->first();

        if (! $enrollment) {
            return back()->withErrors(['student_enrollment_id' => 'Siswa tidak ditemukan di tahun ajaran aktif.']);
        }

        if ($ekskul->members()->where('student_enrollment_id', $enrollment->id)->exists()) {
            return back()->withErrors(['student_enrollment_id' => 'Siswa sudah menjadi anggota ekskul ini.']);
        }

        $ekskul->members()->create([
            'student_enrollment_id' => $enrollment->id,
            'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? now()->toDateString(),
        ]);

        activity('kesiswaan')->performedOn($ekskul)->log('ekskul_anggota_ditambahkan');

        return back()->with('status', 'Anggota ditambahkan.');
    }

    public function memberDestroy(Extracurricular $ekskul, $member): RedirectResponse
    {
        $this->authorize('update', $ekskul);

        $memberModel = $ekskul->members()->findOrFail($member);

        DB::transaction(function () use ($ekskul, $memberModel) {
            $ekskul->attendances()->where('student_enrollment_id', $memberModel->student_enrollment_id)->delete();
            $memberModel->delete();
        });

        activity('kesiswaan')->performedOn($ekskul)->log('ekskul_anggota_dihapus');

        return back()->with('status', 'Anggota dihapus beserta riwayat presensinya.');
    }

    public function presensi(Request $request, Extracurricular $ekskul): RedirectResponse
    {
        $this->authorize('update', $ekskul);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'statuses' => ['required', 'array'],
            'statuses.*.status' => ['required', 'in:hadir,izin,sakit,alpha'],
            'statuses.*.predikat' => ['nullable', 'in:A,B,C,D'],
        ]);

        $memberIds = $ekskul->members()->pluck('student_enrollment_id');

        DB::transaction(function () use ($validated, $ekskul, $memberIds) {
            foreach ($validated['statuses'] as $enrollmentId => $data) {
                if (! $memberIds->contains((int) $enrollmentId)) {
                    continue; // bukan anggota — abaikan
                }

                $hadir = $data['status'] === 'hadir';

                ExtracurricularAttendance::updateOrCreate(
                    [
                        'extracurricular_id' => $ekskul->id,
                        'student_enrollment_id' => (int) $enrollmentId,
                        'tanggal' => $validated['tanggal'],
                    ],
                    [
                        'status' => $data['status'],
                        'predikat' => $hadir ? ($data['predikat'] ?? null) : null,
                        'keterangan' => null,
                    ]
                );
            }
        });

        activity('kesiswaan')->performedOn($ekskul)->log('ekskul_presensi_diinput');

        return redirect()->route('ekskul.show', array_merge([$ekskul], ['tanggal' => $validated['tanggal']]))
            ->with('status', 'Presensi ekstrakurikuler tersimpan.');
    }

    protected function pembinaOptions(): array
    {
        return User::where('role', 'guru')->orderBy('name')->get()->pluck('name', 'id')->all();
    }

    protected function eksRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'pembina_id' => ['required', Rule::exists('users', 'id')->where('role', 'guru')],
            'hari' => ['nullable', 'in:'.implode(',', $this->hariList)],
            'waktu' => ['nullable', 'date_format:H:i'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (Extracurricular::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
