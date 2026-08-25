<?php

namespace App\Http\Controllers\Tu;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Letter;
use App\Models\LetterCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterController extends Controller
{
    /**
     * Tampilkan daftar surat masuk/keluar
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Letter::class);

        $type = $request->get('type', 'masuk');
        $tahun = AcademicYear::active();

        $query = Letter::query()
            ->where('academic_year_id', $tahun->id)
            ->where('type', $type);

        // Filter status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter prioritas
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter tanggal
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }

        // Search
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $letters = $query->latest('date')->latest('id')->paginate(15)->withQueryString();
        $categories = LetterCategory::orderBy('sort_order')->get();

        return view('pages.tu.surat.index', [
            'roleLabel' => 'Tata Usaha',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => $type === 'masuk' ? 'Surat Masuk' : 'Surat Keluar'],
            ],
            'type' => $type,
            'letters' => $letters,
            'categories' => $categories,
            'statuses' => Letter::STATUSES,
            'priorities' => Letter::PRIORITIES,
        ]);
    }

    /**
     * Form tambah surat baru
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Letter::class);

        $type = $request->get('type', 'masuk');
        $categories = LetterCategory::orderBy('sort_order')->get();
        $number = $type === 'keluar' ? Letter::generateNumber() : null;

        return view('pages.tu.surat.create', [
            'roleLabel' => 'Tata Usaha',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => $type === 'masuk' ? 'Surat Masuk' : 'Surat Keluar', 'href' => route('surat.index', ['type' => $type])],
                ['label' => 'Tambah Surat'],
            ],
            'type' => $type,
            'categories' => $categories,
            'number' => $number,
            'statuses' => Letter::STATUSES,
            'priorities' => Letter::PRIORITIES,
        ]);
    }

    /**
     * Simpan surat baru
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Letter::class);

        $validated = $request->validate([
            'type' => ['required', 'in:masuk,keluar'],
            'number' => ['nullable', 'string', 'max:50'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'from_to' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:'.implode(',', Letter::STATUSES)],
            'priority' => ['required', 'in:'.implode(',', Letter::PRIORITIES)],
            'category' => ['nullable', 'string', 'max:100'],
            'disposition_to' => ['nullable', 'string', 'max:255'],
            'disposition_note' => ['nullable', 'string'],
            'file_url' => ['nullable', 'url', 'max:500'],
        ]);

        // Generate nomor jika surat keluar dan belum diisi
        if ($validated['type'] === 'keluar' && empty($validated['number'])) {
            $validated['number'] = Letter::generateNumber();
        }

        $validated['recorded_by'] = auth()->id();
        $validated['academic_year_id'] = AcademicYear::active()->id;

        $letter = Letter::create($validated);

        activity('tu')->performedOn($letter)->log('surat_dibuat');

        $message = $letter->isMasuk()
            ? 'Surat masuk berhasil disimpan ke papan.'
            : 'Surat keluar berhasil disimpan ke papan.';

        return redirect()->route('surat.show', $letter)
            ->with('status', $message);
    }

    /**
     * Detail surat
     */
    public function show(Letter $letter): View
    {
        $this->authorize('view', $letter);

        $letter->load(['recorder', 'academicYear']);

        return view('pages.tu.surat.show', [
            'roleLabel' => 'Tata Usaha',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => $letter->isMasuk() ? 'Surat Masuk' : 'Surat Keluar', 'href' => route('surat.index', ['type' => $letter->type])],
                ['label' => $letter->subject],
            ],
            'letter' => $letter,
        ]);
    }

    /**
     * Form edit surat
     */
    public function edit(Letter $letter): View
    {
        $this->authorize('update', $letter);

        $categories = LetterCategory::orderBy('sort_order')->get();

        return view('pages.tu.surat.edit', [
            'roleLabel' => 'Tata Usaha',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => $letter->isMasuk() ? 'Surat Masuk' : 'Surat Keluar', 'href' => route('surat.index', ['type' => $letter->type])],
                ['label' => 'Edit Surat'],
            ],
            'letter' => $letter,
            'categories' => $categories,
            'statuses' => Letter::STATUSES,
            'priorities' => Letter::PRIORITIES,
        ]);
    }

    /**
     * Update surat
     */
    public function update(Request $request, Letter $letter): RedirectResponse
    {
        $this->authorize('update', $letter);

        $validated = $request->validate([
            'number' => ['nullable', 'string', 'max:50'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'from_to' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:'.implode(',', Letter::STATUSES)],
            'priority' => ['required', 'in:'.implode(',', Letter::PRIORITIES)],
            'category' => ['nullable', 'string', 'max:100'],
            'disposition_to' => ['nullable', 'string', 'max:255'],
            'disposition_note' => ['nullable', 'string'],
            'file_url' => ['nullable', 'url', 'max:500'],
        ]);

        $letter->update($validated);

        activity('tu')->performedOn($letter)->log('surat_diubah');

        return redirect()->route('surat.show', $letter)
            ->with('status', 'Surat berhasil diperbarui.');
    }

    /**
     * Hapus surat
     */
    public function destroy(Letter $letter): RedirectResponse
    {
        $this->authorize('delete', $letter);

        $letter->delete();

        activity('tu')->log('surat_dihapus');

        return redirect()->route('surat.index', ['type' => $letter->type])
            ->with('status', 'Surat berhasil dihapus dari papan.');
    }

    /**
     * Disposisi surat (hanya super_admin)
     */
    public function disposition(Request $request, Letter $letter): RedirectResponse
    {
        $this->authorize('disposition', $letter);

        $validated = $request->validate([
            'disposition_to' => ['required', 'string', 'max:255'],
            'disposition_note' => ['nullable', 'string'],
            'status' => ['required', 'in:'.implode(',', Letter::STATUSES)],
        ]);

        $letter->update($validated);

        activity('tu')->performedOn($letter)->log('surat_disposisi');

        return redirect()->route('surat.show', $letter)
            ->with('status', 'Surat berhasil didisposisi.');
    }
}
