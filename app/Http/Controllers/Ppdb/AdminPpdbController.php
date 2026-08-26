<?php

namespace App\Http\Controllers\Ppdb;

use App\Exports\PpdbExport;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\NisCounter;
use App\Models\PpdbRegistration;
use App\Models\StudentEnrollment;
use App\Support\PpdbService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdminPpdbController extends Controller
{
    public function index(): View
    {
        $query = PpdbRegistration::with('academicYear');

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('q')) {
            $search = request('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_no', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('pages.ppdb.index', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('dashboard')],
                ['label' => 'Pendaftar'],
            ],
            'registrations' => $registrations,
            'stats' => [
                'total' => PpdbRegistration::count(),
                'submitted' => PpdbRegistration::where('status', 'submitted')->count(),
                'accepted' => PpdbRegistration::where('status', 'accepted')->count(),
                'rejected' => PpdbRegistration::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(PpdbRegistration $registration): View
    {
        $registration->load('academicYear', 'student');

        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();
        $classOptions = $classes->pluck('name', 'name');

        return view('pages.ppdb.show', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('ppdb.index')],
                ['label' => $registration->registration_no],
            ],
            'registration' => $registration,
            'classOptions' => $classOptions,
        ]);
    }

    public function accept(PpdbRegistration $registration): RedirectResponse
    {
        if ($registration->status !== 'submitted') {
            return back()->withErrors(['status' => 'Hanya pendaftar dengan status "submitted" yang bisa diterima.']);
        }

        PpdbService::accept($registration);

        return back()->with('status', $registration->name.' berhasil diterima. Tetapkan NIS di menu Generate NIS.');
    }

    public function reject(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        activity('ppdb')
            ->performedOn($registration)
            ->event('rejected')
            ->log('PPDB ditolak: '.$registration->name);

        return back()->with('status', $registration->name.' ditolak.');
    }

    public function assignClass(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:20',
        ]);

        // Pastikan kelas/rombel benar-benar ada di tabel class_groups
        $classGroup = ClassGroup::where('name', $validated['class_name'])->first();
        if (! $classGroup) {
            return back()->withErrors(['class_name' => 'Kelas "'.$validated['class_name'].'" belum ada. Buat kelas dulu di menu Kelas & Penempatan.']);
        }

        $registration->update([
            'kelas' => $classGroup->grade_level,
            'rombel' => $classGroup->name,
        ]);

        // Create or update enrollment
        if ($registration->student_id && $registration->academic_year_id) {
            StudentEnrollment::updateOrCreate(
                [
                    'student_id' => $registration->student_id,
                    'academic_year_id' => $registration->academic_year_id,
                ],
                [
                    'class_group_id' => $classGroup->id,
                    'status' => 'aktif',
                ]
            );
        }

        activity('ppdb')
            ->performedOn($registration)
            ->event('class_assigned')
            ->log('Kelas ditetapkan: '.$classGroup->name);

        return back()->with('status', 'Kelas/Rombel berhasil ditetapkan ke '.$classGroup->name.'.');
    }

    /**
     * Bulk assign a single class to many selected accepted registrations.
     */
    public function assignClassBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:20',
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'integer|exists:ppdb_registrations,id',
        ]);

        $classGroup = ClassGroup::where('name', $validated['class_name'])->first();
        if (! $classGroup) {
            return back()->withErrors(['class_name' => 'Kelas "'.$validated['class_name'].'" belum ada. Buat kelas dulu di menu Kelas & Penempatan.']);
        }

        $count = 0;
        foreach ($validated['registration_ids'] as $id) {
            $registration = PpdbRegistration::where('id', $id)
                ->where('status', 'accepted')
                ->whereNull('kelas')
                ->first();

            if (! $registration) {
                continue;
            }

            $this->applyClassAssignment($registration, $classGroup);
            $count++;
        }

        activity('ppdb')
            ->event('class_assigned_bulk')
            ->withProperties(['class' => $classGroup->name, 'count' => $count])
            ->log('Penetapan kelas massal: '.$count.' siswa → '.$classGroup->name);

        return back()->with('status', $count.' siswa berhasil ditetapkan ke '.$classGroup->name.'.');
    }

    /**
     * Distribute selected accepted registrations evenly across rombels of a grade level.
     */
    public function assignClassDistribute(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:10',
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'integer|exists:ppdb_registrations,id',
        ]);

        $classes = ClassGroup::where('grade_level', $validated['grade_level'])
            ->orderBy('name')
            ->get();

        if ($classes->isEmpty()) {
            return back()->withErrors(['grade_level' => 'Belum ada kelas untuk tingkat '.$validated['grade_level'].'. Buat kelas dulu di menu Kelas & Penempatan.']);
        }

        $registrations = PpdbRegistration::whereIn('id', $validated['registration_ids'])
            ->where('status', 'accepted')
            ->whereNull('kelas')
            ->orderByRaw('UPPER(name)')
            ->get();

        if ($registrations->isEmpty()) {
            return back()->withErrors(['registration_ids' => 'Tidak ada calon terpilih yang masih perlu ditetapkan kelasnya.']);
        }

        $index = 0;
        $count = 0;
        foreach ($registrations as $registration) {
            $classGroup = $classes[$index % $classes->count()];
            $this->applyClassAssignment($registration, $classGroup);
            $index++;
            $count++;
        }

        activity('ppdb')
            ->event('class_distributed')
            ->withProperties(['grade' => $validated['grade_level'], 'count' => $count])
            ->log('Sebar rata tingkat '.$validated['grade_level'].': '.$count.' siswa ke '.$classes->count().' rombel');

        return back()->with('status', $count.' siswa disebar rata ke '.$classes->count().' rombel tingkat '.$validated['grade_level'].'.');
    }

    /**
     * Shared logic: set kelas/rombel on registration + create/update enrollment.
     */
    protected function applyClassAssignment(PpdbRegistration $registration, ClassGroup $classGroup): void
    {
        $registration->update([
            'kelas' => $classGroup->grade_level,
            'rombel' => $classGroup->name,
        ]);

        if ($registration->student_id && $registration->academic_year_id) {
            StudentEnrollment::updateOrCreate(
                [
                    'student_id' => $registration->student_id,
                    'academic_year_id' => $registration->academic_year_id,
                ],
                [
                    'class_group_id' => $classGroup->id,
                    'status' => 'aktif',
                ]
            );
        }

        activity('ppdb')
            ->performedOn($registration)
            ->event('class_assigned')
            ->log('Kelas ditetapkan: '.$classGroup->name);
    }

    public function generateNis(Request $request): View
    {
        $academicYear = AcademicYear::active();

        $pendingNis = PpdbRegistration::where('academic_year_id', $academicYear?->id)
            ->where('status', 'accepted')
            ->whereNull('nis_nism')
            ->orderByRaw('UPPER(name)')
            ->get();

        $preview = $pendingNis->map(function ($reg) {
            return [
                'registration_no' => $reg->registration_no,
                'name' => $reg->name,
                'preview_nis' => PpdbService::previewNis($reg),
            ];
        });

        return view('pages.ppdb.nis-preview', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('ppdb.index')],
                ['label' => 'Generate NIS'],
            ],
            'preview' => $preview,
            'academicYear' => $academicYear,
            'lastNumber' => $academicYear
                ? NisCounter::firstOrCreate(['academic_year_id' => $academicYear->id], ['last_number' => 0])->last_number
                : 0,
        ]);
    }

    public function updateNisCounter(Request $request): RedirectResponse
    {
        $academicYear = AcademicYear::active();
        if (! $academicYear) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif.']);
        }

        $validated = $request->validate([
            'last_number' => 'required|integer|min:0|max:9999',
        ]);

        NisCounter::updateOrCreate(
            ['academic_year_id' => $academicYear->id],
            ['last_number' => $validated['last_number']]
        );

        activity('ppdb')
            ->event('nis_counter_updated')
            ->log('Nomor urut terakhir NIS diatur: '.$validated['last_number']);

        return back()->with('status', 'Nomor urut terakhir NIS berhasil diatur ke '.$validated['last_number'].'.');
    }

    public function commitNis(): RedirectResponse
    {
        $academicYear = AcademicYear::active();
        if (! $academicYear) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif.']);
        }

        $outcome = PpdbService::batchGenerateNis($academicYear->id);
        $generated = $outcome['generated'];
        $skipped = $outcome['skipped'];

        activity('ppdb')
            ->event('nis_generated')
            ->withProperties(['count' => count($generated), 'skipped' => count($skipped)])
            ->log('NIS digenerate: '.count($generated).' siswa');

        $message = count($generated).' NIS berhasil digenerate.';
        if (count($skipped) > 0) {
            $names = collect($skipped)->pluck('name')->implode(', ');
            $message .= ' '.count($skipped).' dilewati karena NIS bentrok: '.$names;
        }

        return back()->with('status', $message);
    }

    public function assignClassPage(): View
    {
        $academicYear = AcademicYear::active();

        $accepted = PpdbRegistration::where('academic_year_id', $academicYear?->id)
            ->where('status', 'accepted')
            ->whereNull('kelas')
            ->orderByRaw('UPPER(name)')
            ->get();

        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        // Jumlah siswa terdaftar per kelas (dari enrollment + yang sudah diassign di PPDB)
        $classCounts = PpdbRegistration::where('academic_year_id', $academicYear?->id)
            ->where('status', 'accepted')
            ->whereNotNull('kelas')
            ->selectRaw('kelas, rombel, COUNT(*) as total')
            ->groupBy('kelas', 'rombel')
            ->get()
            ->pluck('total', function ($r) {
                return $r->kelas.'-'.$r->rombel;
            });

        // Dropdown kelas yang sudah ada: "I-A (2 siswa)"
        $classOptions = [];
        foreach ($classes as $class) {
            $count = $classCounts->get($class->name, 0);
            $classOptions[$class->name] = $class->name.' ('.$count.' siswa)';
        }

        return view('pages.ppdb.assign-class', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('ppdb.index')],
                ['label' => 'Tentukan Kelas'],
            ],
            'accepted' => $accepted,
            'classes' => $classes,
            'classOptions' => $classOptions,
            'classCounts' => $classCounts,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filename = 'ppdb-export-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new PpdbExport($request->status, $request->academic_year_id),
            $filename
        );
    }
}
