<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeachingJournalRequest;
use App\Http\Requests\UpdateTeachingJournalRequest;
use App\Models\AcademicYear;
use App\Models\TeacherAssignment;
use App\Models\TeachingJournal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JurnalController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TeachingJournal::class);

        $tahun = AcademicYear::active();

        $assignments = auth()->user()->assignments()
            ->with(['classGroup', 'subject'])
            ->withCount('journals')
            ->where('academic_year_id', $tahun->id)
            ->orderByDesc('journals_count')
            ->orderBy('class_group_id')
            ->get();

        return view('pages.guru.jurnal.index', [
            'roleLabel' => 'Guru Mata Pelajaran',
            'breadcrumb' => [['label' => 'Beranda Saya', 'href' => route('dashboard')], ['label' => 'Jurnal Mengajar']],
            'tahun' => $tahun,
            'assignments' => $assignments,
        ]);
    }

    public function show(TeacherAssignment $assignment): View
    {
        $this->authorize('viewAny', TeachingJournal::class);
        abort_unless($this->owns($assignment), 403);

        $tahun = AcademicYear::active();

        $journals = $assignment->journals()
            ->with('recorder')
            ->orderByDesc('journal_date')
            ->orderByDesc('period_no')
            ->paginate(10);

        return view('pages.guru.jurnal.show', [
            'roleLabel' => 'Guru Mata Pelajaran',
            'breadcrumb' => [
                ['label' => 'Beranda Saya', 'href' => route('dashboard')],
                ['label' => 'Jurnal Mengajar', 'href' => route('guru.jurnal.index')],
                ['label' => $assignment->subject->name.' · '.$assignment->classGroup->name],
            ],
            'tahun' => $tahun,
            'assignment' => $assignment,
            'journals' => $journals,
        ]);
    }

    public function store(StoreTeachingJournalRequest $request, TeacherAssignment $assignment): RedirectResponse
    {
        $this->authorize('create', TeachingJournal::class);
        abort_unless($this->owns($assignment), 403);

        $validated = $request->validated();

        if ($this->duplicateExists($assignment, $validated['journal_date'], $validated['period_no'] ?? null)) {
            return back()->withErrors(['journal_date' => 'Jurnal untuk kelas, tanggal, dan jam pelajaran tersebut sudah dicatat.']);
        }

        $journal = TeachingJournal::create([
            'academic_year_id' => $assignment->academic_year_id,
            'teacher_assignment_id' => $assignment->id,
            'journal_date' => $validated['journal_date'],
            'period_no' => $validated['period_no'] ?? null,
            'materi' => $validated['materi'],
            'tujuan' => $validated['tujuan'] ?? null,
            'metode' => $validated['metode'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? null,
            'lampiran' => $request->hasFile('lampiran') ? $request->file('lampiran')->store('jurnal', 'public') : null,
            'status' => $validated['status'],
            'recorded_by' => auth()->id(),
        ]);

        activity('akademik')->performedOn($journal)->log('jurnal_diisi');

        $message = $validated['status'] === 'draft'
            ? 'Jurnal disimpan sebagai draf.'
            : 'Jurnal berhasil disimpan dan disematkan ke papan.';

        return redirect()->route('guru.jurnal.show', $assignment)->with('status', $message);
    }

    public function edit(TeacherAssignment $assignment, TeachingJournal $journal): View
    {
        $this->authorize('update', $journal);
        abort_unless($this->owns($assignment), 403);
        abort_unless($journal->teacher_assignment_id === $assignment->id, 404);

        return view('pages.guru.jurnal.edit', [
            'roleLabel' => 'Guru Mata Pelajaran',
            'breadcrumb' => [
                ['label' => 'Beranda Saya', 'href' => route('dashboard')],
                ['label' => 'Jurnal Mengajar', 'href' => route('guru.jurnal.index')],
                ['label' => 'Ubah Jurnal '.$assignment->subject->name],
            ],
            'assignment' => $assignment,
            'journal' => $journal,
        ]);
    }

    public function update(UpdateTeachingJournalRequest $request, TeacherAssignment $assignment, TeachingJournal $journal): RedirectResponse
    {
        $this->authorize('update', $journal);
        abort_unless($this->owns($assignment), 403);
        abort_unless($journal->teacher_assignment_id === $assignment->id, 404);

        $validated = $request->validated();

        if ($this->duplicateExists($assignment, $validated['journal_date'], $validated['period_no'] ?? null, $journal->id)) {
            return back()->withErrors(['journal_date' => 'Jurnal untuk kelas, tanggal, dan jam pelajaran tersebut sudah dicatat.']);
        }

        $lampiran = $journal->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($lampiran) {
                Storage::disk('public')->delete($lampiran);
            }
            $lampiran = $request->file('lampiran')->store('jurnal', 'public');
        } elseif (! empty($validated['hapus_lampiran'])) {
            if ($lampiran) {
                Storage::disk('public')->delete($lampiran);
            }
            $lampiran = null;
        }

        $journal->update([
            'journal_date' => $validated['journal_date'],
            'period_no' => $validated['period_no'] ?? null,
            'materi' => $validated['materi'],
            'tujuan' => $validated['tujuan'] ?? null,
            'metode' => $validated['metode'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? null,
            'lampiran' => $lampiran,
            'status' => $validated['status'],
        ]);

        activity('akademik')->performedOn($journal)->log('jurnal_diubah');

        return redirect()->route('guru.jurnal.show', $assignment)->with('status', 'Jurnal berhasil diperbarui.');
    }

    public function destroy(TeachingJournal $journal): RedirectResponse
    {
        $this->authorize('delete', $journal);

        $assignmentId = $journal->teacher_assignment_id;

        if ($journal->lampiran) {
            Storage::disk('public')->delete($journal->lampiran);
        }

        $journal->delete();

        activity('akademik')->performedOn($journal)->log('jurnal_dihapus');

        return redirect()->route('guru.jurnal.show', $assignmentId)->with('status', 'Jurnal dihapus dari papan.');
    }

    public function lampiran(TeacherAssignment $assignment, TeachingJournal $journal): StreamedResponse
    {
        $this->authorize('viewAny', TeachingJournal::class);
        abort_unless($this->owns($assignment), 403);
        abort_unless($journal->teacher_assignment_id === $assignment->id, 404);
        abort_unless($journal->lampiran && Storage::disk('public')->exists($journal->lampiran), 404);

        return Storage::disk('public')->download($journal->lampiran);
    }

    protected function owns(TeacherAssignment $assignment): bool
    {
        return $assignment->user_id === auth()->id();
    }

    protected function duplicateExists(TeacherAssignment $assignment, string $date, ?int $period, ?int $ignoreId = null): bool
    {
        $query = TeachingJournal::where('teacher_assignment_id', $assignment->id)
            ->where('journal_date', $date);

        if ($period) {
            $query->where('period_no', $period);
        } else {
            $query->whereNull('period_no');
        }

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
