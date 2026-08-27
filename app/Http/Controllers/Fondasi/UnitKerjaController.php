<?php

namespace App\Http\Controllers\Fondasi;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitKerjaController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', OrganizationalUnit::class);

        $units = OrganizationalUnit::withCount('employees')->orderBy('code')->get();

        return view('pages.fondasi.unit-kerja.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Unit Kerja'],
            ],
            'units' => $units,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', OrganizationalUnit::class);

        return view('pages.fondasi.unit-kerja.form', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Unit Kerja', 'href' => route('unit-kerja.index')],
                ['label' => 'Tambah Unit'],
            ],
            'editing' => false,
            'unit' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', OrganizationalUnit::class);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:organizational_units,code'],
            'name' => ['required', 'string', 'max:60'],
        ]);

        OrganizationalUnit::create($validated);

        return redirect()->route('unit-kerja.index')->with('status', 'Unit kerja berhasil ditambahkan.');
    }

    public function show(OrganizationalUnit $unit): View
    {
        $this->authorize('view', $unit);

        $unit->load(['employees.person', 'employees.position']);

        return view('pages.fondasi.unit-kerja.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Unit Kerja', 'href' => route('unit-kerja.index')],
                ['label' => $unit->name],
            ],
            'unit' => $unit,
        ]);
    }

    public function edit(OrganizationalUnit $unit): View
    {
        $this->authorize('update', $unit);

        return view('pages.fondasi.unit-kerja.form', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Unit Kerja', 'href' => route('unit-kerja.index')],
                ['label' => 'Ubah '.$unit->name],
            ],
            'editing' => true,
            'unit' => $unit,
        ]);
    }

    public function update(Request $request, OrganizationalUnit $unit): RedirectResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:organizational_units,code,'.$unit->id],
            'name' => ['required', 'string', 'max:60'],
        ]);

        $unit->update($validated);

        return redirect()->route('unit-kerja.index')->with('status', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy(OrganizationalUnit $unit): RedirectResponse
    {
        $this->authorize('delete', $unit);

        if ($unit->employees()->exists()) {
            return back()->withErrors(['error' => 'Unit kerja tidak dapat dihapus karena masih memiliki pegawai.']);
        }

        $unit->delete();

        return redirect()->route('unit-kerja.index')->with('status', 'Unit kerja berhasil dihapus.');
    }
}
