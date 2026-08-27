<?php

namespace App\Http\Controllers\Fondasi;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Position::class);

        $positions = Position::withCount('employees')->orderBy('code')->get();

        return view('pages.fondasi.jabatan.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Jabatan'],
            ],
            'positions' => $positions,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Position::class);

        return view('pages.fondasi.jabatan.form', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Jabatan', 'href' => route('jabatan.index')],
                ['label' => 'Tambah Jabatan'],
            ],
            'editing' => false,
            'position' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Position::class);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:positions,code'],
            'name' => ['required', 'string', 'max:60'],
        ]);

        Position::create($validated);

        return redirect()->route('jabatan.index')->with('status', 'Jabatan berhasil ditambahkan.');
    }

    public function show(Position $position): View
    {
        $this->authorize('view', $position);

        $position->load(['employees.person', 'employees.organizationalUnit']);

        return view('pages.fondasi.jabatan.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Jabatan', 'href' => route('jabatan.index')],
                ['label' => $position->name],
            ],
            'position' => $position,
        ]);
    }

    public function edit(Position $position): View
    {
        $this->authorize('update', $position);

        return view('pages.fondasi.jabatan.form', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Jabatan', 'href' => route('jabatan.index')],
                ['label' => 'Ubah '.$position->name],
            ],
            'editing' => true,
            'position' => $position,
        ]);
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $this->authorize('update', $position);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:positions,code,'.$position->id],
            'name' => ['required', 'string', 'max:60'],
        ]);

        $position->update($validated);

        return redirect()->route('jabatan.index')->with('status', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $this->authorize('delete', $position);

        if ($position->employees()->exists()) {
            return back()->withErrors(['error' => 'Jabatan tidak dapat dihapus karena masih memiliki pegawai.']);
        }

        $position->delete();

        return redirect()->route('jabatan.index')->with('status', 'Jabatan berhasil dihapus.');
    }
}
