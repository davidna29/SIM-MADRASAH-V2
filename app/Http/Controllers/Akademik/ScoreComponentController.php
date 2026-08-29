<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScoreComponentRequest;
use App\Http\Requests\UpdateScoreComponentRequest;
use App\Models\AcademicYear;
use App\Models\ScoreComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScoreComponentController extends Controller
{
    public function index(): View
    {
        $tahun = AcademicYear::active();

        $components = ScoreComponent::withCount('values')
            ->where('academic_year_id', $tahun->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $totalWeight = (float) $components->sum('weight');

        return view('pages.akademik.komponen-nilai.index', [
            'roleLabel' => 'Wakamad Kurikulum',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Komponen Nilai'],
            ],
            'tahun' => $tahun,
            'components' => $components,
            'totalWeight' => $totalWeight,
        ]);
    }

    public function store(StoreScoreComponentRequest $request): RedirectResponse
    {
        $tahun = AcademicYear::active();

        $validated = $request->validated();
        $validated['academic_year_id'] = $tahun->id;
        $validated['sort_order'] = (ScoreComponent::where('academic_year_id', $tahun->id)->max('sort_order') ?? 0) + 1;

        $component = ScoreComponent::create($validated);

        activity('akademik')->performedOn($component)->log('komponen_nilai_baru');

        return redirect()->route('akademik.komponen-nilai.index')->with('status', 'Komponen nilai berhasil ditambahkan.');
    }

    public function update(UpdateScoreComponentRequest $request, ScoreComponent $scoreComponent): RedirectResponse
    {
        $scoreComponent->update($request->validated());

        activity('akademik')->performedOn($scoreComponent)->log('komponen_nilai_diubah');

        return redirect()->route('akademik.komponen-nilai.index')->with('status', 'Komponen nilai berhasil diperbarui.');
    }

    public function destroy(ScoreComponent $scoreComponent): RedirectResponse
    {
        if ($scoreComponent->values()->exists()) {
            return back()->withErrors(['delete' => "Komponen {$scoreComponent->name} sudah dipakai pada nilai siswa dan tidak dapat dihapus."]);
        }

        $scoreComponent->delete();

        activity('akademik')->performedOn($scoreComponent)->log('komponen_nilai_dihapus');

        return redirect()->route('akademik.komponen-nilai.index')->with('status', 'Komponen nilai dihapus.');
    }
}
