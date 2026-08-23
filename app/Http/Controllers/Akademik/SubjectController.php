<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Subject::class);

        $subjects = Subject::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('code')
            ->paginate(20)
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

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Subject::class);

        $validated = $request->validated();
        $validated['sort_order'] = (Subject::max('sort_order') ?? 0) + 1;

        $subject = Subject::create($validated);

        activity('akademik')->performedOn($subject)->log('mapel_baru');

        return redirect()->route('mapel.index')->with('status', 'Mata pelajaran berhasil disimpan dan disematkan ke papan.');
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        $subject->update($request->validated());

        activity('akademik')->performedOn($subject)->log('mapel_diubah');

        return redirect()->route('mapel.index')->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $this->authorize('create', Subject::class);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:subjects,id'],
        ]);

        foreach ($validated['order'] as $position => $id) {
            Subject::whereKey($id)->update(['sort_order' => $position + 1]);
        }

        activity('akademik')->log('mapel_diurutkan');

        return back()->with('status', 'Urutan mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $this->authorize('delete', $subject);

        if ($subject->assignments()->exists() || $subject->scores()->exists()) {
            return back()->withErrors(['delete' => "Mata pelajaran {$subject->name} sudah dipakai pada penugasan/nilai dan tidak dapat dihapus."]);
        }

        $subject->delete();

        activity('akademik')->performedOn($subject)->log('mapel_dihapus');

        return redirect()->route('mapel.index')->with('status', 'Mata pelajaran dihapus.');
    }
}
