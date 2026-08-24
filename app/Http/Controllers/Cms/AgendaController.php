<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Agenda::class);

        $agenda = Agenda::with('creator')
            ->when(request('jenis'), fn ($q, $j) => $q->where('jenis', $j))
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.cms.agenda.index', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda'],
                ['label' => 'Agenda & Pengumuman'],
            ],
            'agenda' => $agenda,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Agenda::class);

        return view('pages.cms.agenda.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.agenda.index')],
                ['label' => 'Tambah Agenda'],
            ],
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Agenda::class);

        $validated = $request->validate($this->rules());

        $agenda = Agenda::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        activity('publikasi')->performedOn($agenda)->log('agenda_dibuat');

        return redirect()->route('cms.agenda.index')->with('status', 'Agenda/Pengumuman disimpan.');
    }

    public function edit(Agenda $agenda): View
    {
        $this->authorize('update', $agenda);

        return view('pages.cms.agenda.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.agenda.index')],
                ['label' => 'Ubah Agenda'],
            ],
            'editing' => true,
            'agenda' => $agenda,
        ]);
    }

    public function update(Request $request, Agenda $agenda): RedirectResponse
    {
        $this->authorize('update', $agenda);

        $validated = $request->validate($this->rules());

        $agenda->update($validated);

        activity('publikasi')->performedOn($agenda)->log('agenda_diubah');

        return redirect()->route('cms.agenda.index')->with('status', 'Agenda/Pengumuman diperbarui.');
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        $this->authorize('delete', $agenda);

        $agenda->delete();

        activity('publikasi')->performedOn($agenda)->log('agenda_dihapus');

        return redirect()->route('cms.agenda.index')->with('status', 'Agenda/Pengumuman dihapus.');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:agenda,pengumuman'],
            'tanggal' => ['nullable', 'date'],
            'waktu' => ['nullable', 'date_format:H:i'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'penanggung_jawab' => ['nullable', 'string', 'max:100'],
            'isi' => ['nullable', 'string'],
            'target' => ['required', 'in:publik,internal'],
            'tampil_mulai' => ['required', 'date'],
            'tampil_selesai' => ['nullable', 'date', 'after_or_equal:tampil_mulai'],
            'status' => ['required', 'in:aktif,arsip'],
        ];
    }
}
