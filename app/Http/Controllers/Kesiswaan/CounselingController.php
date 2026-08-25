<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCounselingSessionRequest;
use App\Http\Requests\UpdateCounselingSessionRequest;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\CounselingSession;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CounselingController extends Controller
{
    private const COUNSELING_TYPES = [
        'individual' => 'Individual',
        'kelompok' => 'Kelompok',
        'krisis' => 'Krisis',
    ];

    private const CONFIDENTIALITY_LEVELS = [
        'guru_bk_only' => 'Hanya Guru BK',
        'plus_kepala' => 'Guru BK & Kepala Madrasah',
        'plus_wali_kelas' => 'Guru BK, Kepala & Wali Kelas',
    ];

    public function index(): View
    {
        $this->authorize('viewAny', CounselingSession::class);

        $user = auth()->user();
        $tahun = AcademicYear::active();

        $sessions = CounselingSession::with('enrollment.student', 'counselor')
            ->visibleTo($user)
            ->when(request('class_group_id'), fn ($q, $id) => $q->whereHas('enrollment', fn ($e) => $e
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $id)
                ->where('status', 'aktif')))
            ->when(request('confidentiality_level'), fn ($q, $v) => $q->where('confidentiality_level', $v))
            ->when(request('status'), fn ($q, $v) => $q->where('status', $v))
            ->when(request('q'), function ($q, $s) {
                $q->whereHas('enrollment.student', fn ($st) => $st
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('nis', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.kesiswaan.konseling.index', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Konseling (BK)'],
            ],
            'sessions' => $sessions,
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
            'confidentialityLevels' => self::CONFIDENTIALITY_LEVELS,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CounselingSession::class);

        $selectedClassId = request('class_group_id') ? (int) request('class_group_id') : null;

        return view('pages.kesiswaan.konseling.create', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Konseling (BK)', 'href' => route('konseling.index')],
                ['label' => 'Catat Konseling'],
            ],
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
            'selectedClassId' => $selectedClassId,
            'students' => $this->formStudents($selectedClassId),
            'counselingTypes' => self::COUNSELING_TYPES,
            'confidentialityLevels' => self::CONFIDENTIALITY_LEVELS,
        ]);
    }

    public function store(StoreCounselingSessionRequest $request): RedirectResponse
    {
        $this->authorize('create', CounselingSession::class);

        $validated = $request->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('counseling', 'local');
        }

        $session = CounselingSession::create([
            ...$validated,
            'counselor_user_id' => auth()->id(),
            'attachment_path' => $attachmentPath,
        ]);

        activity('kesiswaan')
            ->performedOn($session)
            ->withProperties(['siswa' => $session->enrollment->student->displayName()])
            ->log('konseling_dicatat');

        return redirect()->route('konseling.index')->with('status', 'Sesi konseling berhasil dicatat.');
    }

    public function show(CounselingSession $session): View
    {
        $this->authorize('view', $session);

        return view('pages.kesiswaan.konseling.show', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Konseling (BK)', 'href' => route('konseling.index')],
                ['label' => 'Detail Sesi Konseling'],
            ],
            'session' => $session->load(['enrollment.student', 'enrollment.classGroup', 'counselor']),
            'counselingTypes' => self::COUNSELING_TYPES,
            'confidentialityLevels' => self::CONFIDENTIALITY_LEVELS,
        ]);
    }

    public function edit(CounselingSession $session): View
    {
        $this->authorize('update', $session);

        $tahun = AcademicYear::active();
        $selectedClassId = StudentEnrollment::where('id', $session->student_enrollment_id)
            ->value('class_group_id');

        return view('pages.kesiswaan.konseling.edit', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Konseling (BK)', 'href' => route('konseling.index')],
                ['label' => 'Ubah Sesi Konseling'],
            ],
            'session' => $session,
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
            'selectedClassId' => $selectedClassId,
            'students' => $this->formStudents($selectedClassId),
            'counselingTypes' => self::COUNSELING_TYPES,
            'confidentialityLevels' => self::CONFIDENTIALITY_LEVELS,
        ]);
    }

    public function update(UpdateCounselingSessionRequest $request, CounselingSession $session): RedirectResponse
    {
        $this->authorize('update', $session);

        $validated = $request->validated();

        $attachmentPath = $session->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('counseling', 'local');
        }

        $session->update([
            ...$validated,
            'attachment_path' => $attachmentPath,
        ]);

        activity('kesiswaan')
            ->performedOn($session)
            ->withProperties(['siswa' => $session->enrollment->student->displayName()])
            ->log('konseling_diubah');

        return redirect()->route('konseling.show', $session)->with('status', 'Sesi konseling berhasil diperbarui.');
    }

    public function destroy(CounselingSession $session): RedirectResponse
    {
        $this->authorize('delete', $session);

        if ($session->attachment_path) {
            Storage::disk('local')->delete($session->attachment_path);
        }

        $nama = $session->enrollment->student->displayName();
        $session->delete();

        activity('kesiswaan')
            ->withProperties(['siswa' => $nama])
            ->log('konseling_dihapus');

        return redirect()->route('konseling.index')->with('status', 'Sesi konseling dihapus.');
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
            ->mapWithKeys(fn ($e) => [$e->id => $e->student->displayName()])
            ->all();
    }
}
