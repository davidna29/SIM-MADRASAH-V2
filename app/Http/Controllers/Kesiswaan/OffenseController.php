<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Offense;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OffenseController extends Controller
{
    protected array $tingkatList = ['ringan', 'sedang', 'berat'];

    public function index(): View
    {
        $this->authorize('viewAny', Offense::class);

        $tahun = AcademicYear::active();

        $offenses = Offense::with('student')
            ->when(request('class_group_id'), fn ($q, $id) => $q->whereHas('student.enrollments', fn ($e) => $e
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $id)
                ->where('status', 'aktif')))
            ->when(request('tingkat'), fn ($q, $v) => $q->where('tingkat', $v))
            ->when(request('status_penyelesaian'), fn ($q, $v) => $q->where('status_penyelesaian', $v))
            ->when(request('q'), fn ($q, $s) => $q->whereHas('student', fn ($s2) => $s2
                ->where('name', 'like', "%{$s}%")
                ->orWhere('nis', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.kesiswaan.pelanggaran.index', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran'],
                ['label' => 'Pelanggaran'],
            ],
            'offenses' => $offenses,
            'classes' => $this->classes(),
            'tingkatList' => $this->tingkatList,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Offense::class);

        $selectedClassId = request('class_group_id') ? (int) request('class_group_id') : null;

        return view('pages.kesiswaan.pelanggaran.form', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran', 'href' => route('pelanggaran.index')],
                ['label' => 'Catat Pelanggaran'],
            ],
            'editing' => false,
            'classes' => $this->classes(),
            'selectedClassId' => $selectedClassId,
            'students' => $this->formStudents($selectedClassId),
            'tingkatList' => $this->tingkatList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Offense::class);

        $validated = $request->validate($this->rules());

        $offense = Offense::create([
            ...$validated,
            'pemanggilan_ortu' => $request->boolean('pemanggilan_ortu'),
            'bukti' => $request->hasFile('bukti') ? $request->file('bukti')->store('kesiswaan', 'public') : null,
            'status_penyelesaian' => 'proses',
            'created_by' => auth()->id(),
        ]);

        activity('kesiswaan')->performedOn($offense)->log('pelanggaran_dicatat');

        return redirect()->route('pelanggaran.index')->with('status', 'Pelanggaran dicatat.');
    }

    public function edit(Offense $offense): View
    {
        $this->authorize('update', $offense);

        $tahun = AcademicYear::active();
        $selectedClassId = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('student_id', $offense->student_id)
            ->where('status', 'aktif')
            ->value('class_group_id');

        return view('pages.kesiswaan.pelanggaran.form', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran', 'href' => route('pelanggaran.index')],
                ['label' => 'Ubah Pelanggaran'],
            ],
            'editing' => true,
            'offense' => $offense,
            'classes' => $this->classes(),
            'selectedClassId' => $selectedClassId,
            'students' => $this->formStudents($selectedClassId),
            'tingkatList' => $this->tingkatList,
        ]);
    }

    public function update(Request $request, Offense $offense): RedirectResponse
    {
        $this->authorize('update', $offense);

        $validated = $request->validate($this->rules());

        $bukti = $offense->bukti;
        if ($request->hasFile('bukti')) {
            if ($bukti) {
                Storage::disk('public')->delete($bukti);
            }
            $bukti = $request->file('bukti')->store('kesiswaan', 'public');
        }

        $offense->update([
            ...$validated,
            'pemanggilan_ortu' => $request->boolean('pemanggilan_ortu'),
            'bukti' => $bukti,
        ]);

        activity('kesiswaan')->performedOn($offense)->log('pelanggaran_diubah');

        return redirect()->route('pelanggaran.index')->with('status', 'Pelanggaran diperbarui.');
    }

    public function destroy(Offense $offense): RedirectResponse
    {
        $this->authorize('delete', $offense);

        if ($offense->bukti) {
            Storage::disk('public')->delete($offense->bukti);
        }

        $offense->delete();

        activity('kesiswaan')->performedOn($offense)->log('pelanggaran_dihapus');

        return redirect()->route('pelanggaran.index')->with('status', 'Pelanggaran dihapus.');
    }

    protected function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'kategori' => ['required', 'string', 'max:50'],
            'tingkat' => ['required', 'in:'.implode(',', $this->tingkatList)],
            'poin' => ['required', 'integer', 'between:0,100'],
            'tanggal_kejadian' => ['required', 'date'],
            'kronologi' => ['required', 'string', 'max:2000'],
            'pelapor' => ['nullable', 'string', 'max:100'],
            'bukti' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'tindakan' => ['nullable', 'string', 'max:255'],
            'pemanggilan_ortu' => ['nullable', 'boolean'],
            'surat_peringatan' => ['nullable', 'in:sp1,sp2,sp3'],
            'status_penyelesaian' => ['required', 'in:proses,selesai,dibebaskan'],
        ];
    }

    protected function classes()
    {
        return ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();
    }

    protected function formStudents(?int $classId = null): array
    {
        $tahun = AcademicYear::active();

        if (! $classId) {
            return [];
        }

        return StudentEnrollment::with('student')
            ->where('academic_year_id', $tahun->id)
            ->where('class_group_id', $classId)
            ->where('status', 'aktif')
            ->orderBy('student_id')
            ->get()
            ->mapWithKeys(fn ($e) => [$e->student_id => $e->student->displayName()])
            ->all();
    }
}
