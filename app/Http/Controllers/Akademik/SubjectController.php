<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Subject::class);

        $subjects = Subject::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
            ->orderBy('code')
            ->paginate(12)
            ->withQueryString();

        return view('pages.mapel.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Mata Pelajaran'],
            ],
            'subjects' => $subjects,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Subject::class);

        return view('pages.mapel.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Mata Pelajaran', 'href' => route('mapel.index')],
                ['label' => 'Tambah Mata Pelajaran'],
            ],
            'editing' => false,
        ]);
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Subject::class);

        $subject = Subject::create($request->validated());

        activity('akademik')->performedOn($subject)->log('mapel_baru');

        return redirect()->route('mapel.index')->with('status', 'Mata pelajaran berhasil disimpan dan disematkan ke papan.');
    }

    public function edit(Subject $subject): View
    {
        $this->authorize('update', $subject);

        return view('pages.mapel.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Mata Pelajaran', 'href' => route('mapel.index')],
                ['label' => 'Ubah '.$subject->name],
            ],
            'editing' => true,
            'subject' => $subject,
        ]);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        $subject->update($request->validated());

        activity('akademik')->performedOn($subject)->log('mapel_diubah');

        return redirect()->route('mapel.index')->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        activity('akademik')->performedOn($subject)->log('mapel_dihapus');

        return redirect()->route('mapel.index')->with('status', 'Mata pelajaran dihapus.');
    }
}
