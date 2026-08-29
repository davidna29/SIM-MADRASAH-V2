<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMutasiKeluarRequest;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentMutation;
use App\Support\MutasiKeluarService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MutasiKeluarController extends Controller
{
    public function index(): View
    {
        $tahun = AcademicYear::active();

        $mutations = StudentMutation::query()
            ->with(['student.person', 'academicYear'])
            ->when(request('academic_year_id'), fn ($q, $id) => $q->where('academic_year_id', $id))
            ->when(request('q'), fn ($q, $s) => $q->whereHas('student', function ($w) use ($s) {
                $w->where('nis', 'like', "%{$s}%")
                    ->orWhereHas('person', fn ($p) => $p->where('name', 'like', "%{$s}%"));
            }))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pages.mutasi-keluar.index', [
            'roleLabel' => 'Akademik',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('siswa.index')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => 'Mutasi Siswa Keluar'],
            ],
            'mutations' => $mutations,
            'tahun' => $tahun,
            'years' => AcademicYear::orderByDesc('name')->pluck('name', 'id'),
            'stats' => [
                'total' => StudentMutation::count(),
                'thisYear' => StudentMutation::where('academic_year_id', $tahun?->id)->count(),
                'recent' => StudentMutation::where('academic_year_id', $tahun?->id)
                    ->whereDate('tanggal_mutasi', '>=', now()->subMonths(3))
                    ->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $tahun = AcademicYear::active();

        $students = $this->activeStudents($tahun);

        return view('pages.mutasi-keluar.create', [
            'roleLabel' => 'Akademik',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('siswa.index')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => 'Mutasi Siswa Keluar', 'href' => route('mutasi-keluar.index')],
                ['label' => 'Tambah'],
            ],
            'editing' => false,
            'students' => $students,
            'studentPool' => $students->map(fn ($s) => [
                'id' => $s->id,
                'label' => $s->displayName(),
                'nis' => $s->nis ?? 'tanpa NIS',
                'kelas' => $s->enrollments->first()?->classGroup?->name ?? 'Tanpa rombel',
            ])->values(),
            'tahun' => $tahun,
        ]);
    }

    public function store(StoreMutasiKeluarRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $mutation = MutasiKeluarService::create($validated, AcademicYear::active()->id, $request->user()?->id);

        return redirect()->route('mutasi-keluar.show', $mutation)
            ->with('status', 'Mutasi keluar '.$mutation->student->displayName().' berhasil dicatat.');
    }

    public function show(StudentMutation $mutation): View
    {
        $mutation->load(['student.person', 'academicYear', 'creator']);

        return view('pages.mutasi-keluar.show', [
            'roleLabel' => 'Akademik',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('siswa.index')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => 'Mutasi Siswa Keluar', 'href' => route('mutasi-keluar.index')],
                ['label' => $mutation->student->displayName()],
            ],
            'mutation' => $mutation,
        ]);
    }

    public function edit(StudentMutation $mutation): View
    {
        return view('pages.mutasi-keluar.create', [
            'roleLabel' => 'Akademik',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('siswa.index')],
                ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                ['label' => 'Mutasi Siswa Keluar', 'href' => route('mutasi-keluar.index')],
                ['label' => 'Ubah '.$mutation->student->displayName()],
            ],
            'editing' => true,
            'mutation' => $mutation->load('student.person'),
            'students' => collect([$mutation->student]),
            'tahun' => AcademicYear::active(),
        ]);
    }

    public function update(StoreMutasiKeluarRequest $request, StudentMutation $mutation): RedirectResponse
    {
        $validated = $request->validated();

        // Update hanya metadata; status enrollment tidak disentuh di sini.
        $mutation->update($validated);

        activity('mutasi')
            ->performedOn($mutation)
            ->event('updated')
            ->log('Mutasi keluar diperbarui: '.$mutation->student->displayName());

        return redirect()->route('mutasi-keluar.show', $mutation)
            ->with('status', 'Data mutasi keluar berhasil diperbarui.');
    }

    public function destroy(StudentMutation $mutation): RedirectResponse
    {
        MutasiKeluarService::undo($mutation);

        return redirect()->route('mutasi-keluar.index')
            ->with('status', 'Mutasi keluar dibatalkan. Status siswa dikembalikan ke aktif (bila masih keluar).');
    }

    /** Siswa yang masih ter-enroll aktif pada tahun berjalan (untuk form pilih). */
    protected function activeStudents(?AcademicYear $tahun): Collection
    {
        if (! $tahun) {
            return collect();
        }

        return Student::query()
            ->with(['person', 'enrollments' => fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif')->with('classGroup')])
            ->whereHas('enrollments', fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif'))
            ->orderBy('nis')
            ->get();
    }
}
