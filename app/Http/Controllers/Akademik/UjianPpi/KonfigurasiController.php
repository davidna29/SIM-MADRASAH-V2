<?php

namespace App\Http\Controllers\Akademik\UjianPpi;

use App\Http\Controllers\Controller;
use App\Models\PpiExamAspect;
use App\Models\PpiExamAspectCategory;
use App\Models\PpiExamHafalanMateri;
use App\Models\PpiExamHafalanScore;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamPredicateScale;
use App\Models\PpiExamScore;
use App\Services\PpiExamScoringService;
use App\Services\PpiExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KonfigurasiController extends Controller
{
    public function __construct(
        protected PpiExamService $service,
        protected PpiExamScoringService $scoring,
    ) {}

    // ============ Skala Predikat ============

    public function skala(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        return view('pages.ujianppi.konfigurasi.skala', [
            'periode' => $periode,
            'scales' => $periode->scales()->orderBy('urutan')->get(),
            'editable' => $this->editable($periode),
        ]);
    }

    public function skalaStore(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $this->validateScale($request);

        $this->assertNoOverlap($periode, $validated, null);

        PpiExamPredicateScale::create($validated + ['exam_period_id' => $periode->id]);

        $this->afterConfigChange($periode, 'ujian_ppi_skala_ditambah');

        return redirect()->route('ujianppi.konfigurasi.skala', $periode)->with('status', 'Skala predikat ditambahkan.');
    }

    public function skalaUpdate(Request $request, PpiExamPeriod $periode, PpiExamPredicateScale $scale): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $this->validateScale($request);

        $this->assertNoOverlap($periode, $validated, $scale->id);

        $scale->update($validated);

        $this->afterConfigChange($periode, 'ujian_ppi_skala_diubah');

        return redirect()->route('ujianppi.konfigurasi.skala', $periode)->with('status', 'Skala predikat diperbarui.');
    }

    public function skalaDestroy(PpiExamPeriod $periode, PpiExamPredicateScale $scale): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $scale->delete();
        $this->afterConfigChange($periode, 'ujian_ppi_skala_dihapus');

        return redirect()->route('ujianppi.konfigurasi.skala', $periode)->with('status', 'Skala predikat dihapus.');
    }

    // ============ Bobot Penilaian ============

    public function bobot(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        return view('pages.ujianppi.konfigurasi.bobot', [
            'periode' => $periode,
            'editable' => $this->editable($periode),
        ]);
    }

    public function bobotUpdate(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'bobot_p1' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_p2' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_p3' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_hafalan' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $total = array_sum($validated);

        if ($total !== 100) {
            return back()->withErrors(['bobot' => 'Total bobot harus 100%, saat ini '.$total.'%.'])->withInput();
        }

        $periode->update($validated);
        $this->scoring->recomputePeriod($periode);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_bobot_diubah');

        return redirect()->route('ujianppi.konfigurasi.bobot', $periode)->with('status', 'Bobot penilaian disimpan.');
    }

    // ============ Struktur Aspek ============

    public function aspek(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        return view('pages.ujianppi.konfigurasi.aspek', [
            'periode' => $periode,
            'categories' => $periode->categories()->with('aspects')->orderBy('penguji_urutan')->orderBy('urutan')->get(),
            'editable' => $this->editable($periode),
        ]);
    }

    public function aspekStore(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:150'],
            'penguji_urutan' => ['required', 'in:1,2,3'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        PpiExamAspectCategory::create($validated + ['exam_period_id' => $periode->id]);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_aspek_ditambah');

        return redirect()->route('ujianppi.konfigurasi.aspek', $periode)->with('status', 'Kategori aspek ditambahkan.');
    }

    public function aspekUpdate(Request $request, PpiExamPeriod $periode, PpiExamAspectCategory $category): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:150'],
            'penguji_urutan' => ['required', 'in:1,2,3'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        $category->update($validated);

        $this->afterConfigChange($periode, 'ujian_ppi_aspek_diubah');

        return redirect()->route('ujianppi.konfigurasi.aspek', $periode)->with('status', 'Kategori aspek diperbarui.');
    }

    public function aspekDestroy(PpiExamPeriod $periode, PpiExamAspectCategory $category): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        if ($this->categoryHasScores($category)) {
            return back()->withErrors(['aspek' => 'Kategori ini sudah memiliki nilai; hapus nilai di Rekap terlebih dahulu.']);
        }

        $category->delete();
        $this->afterConfigChange($periode, 'ujian_ppi_aspek_dihapus');

        return redirect()->route('ujianppi.konfigurasi.aspek', $periode)->with('status', 'Kategori aspek dihapus.');
    }

    public function aspekItemStore(Request $request, PpiExamPeriod $periode, PpiExamAspectCategory $category): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:150'],
        ]);

        $category->aspects()->create($validated + [
            'urutan' => ($category->aspects()->max('urutan') ?? 0) + 1,
        ]);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_aspek_item_ditambah');

        return redirect()->route('ujianppi.konfigurasi.aspek', $periode)->with('status', 'Item aspek ditambahkan.');
    }

    public function aspekItemUpdate(Request $request, PpiExamPeriod $periode, PpiExamAspect $aspect): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:150'],
        ]);

        $aspect->update($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_aspek_item_diubah');

        return redirect()->route('ujianppi.konfigurasi.aspek', $periode)->with('status', 'Item aspek diperbarui.');
    }

    public function aspekItemDestroy(PpiExamPeriod $periode, PpiExamAspect $aspect): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $hasScore = PpiExamScore::where('aspect_id', $aspect->id)->exists();
        if ($hasScore) {
            return back()->withErrors(['aspek' => 'Item ini sudah memiliki nilai; hapus nilai di Rekap terlebih dahulu.']);
        }

        $aspect->delete();
        $this->afterConfigChange($periode, 'ujian_ppi_aspek_item_dihapus');

        return redirect()->route('ujianppi.konfigurasi.aspek', $periode)->with('status', 'Item aspek dihapus.');
    }

    // ============ Materi Setoran ============

    public function hafalan(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        return view('pages.ujianppi.konfigurasi.hafalan', [
            'periode' => $periode,
            'materi' => $periode->hafalanMateri()->get(),
            'editable' => $this->editable($periode),
        ]);
    }

    public function hafalanStore(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
        ]);

        $periode->hafalanMateri()->create($validated + [
            'urutan' => ($periode->hafalanMateri()->max('urutan') ?? 0) + 1,
        ]);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_hafalan_ditambah');

        return redirect()->route('ujianppi.konfigurasi.hafalan', $periode)->with('status', 'Materi setoran ditambahkan.');
    }

    public function hafalanUpdate(Request $request, PpiExamPeriod $periode, PpiExamHafalanMateri $materi): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
        ]);

        $materi->update($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_hafalan_diubah');

        return redirect()->route('ujianppi.konfigurasi.hafalan', $periode)->with('status', 'Materi setoran diperbarui.');
    }

    public function hafalanDestroy(PpiExamPeriod $periode, PpiExamHafalanMateri $materi): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $hasScore = PpiExamHafalanScore::where('hafalan_materi_id', $materi->id)->exists();
        if ($hasScore) {
            return back()->withErrors(['hafalan' => 'Materi ini sudah memiliki nilai setoran.']);
        }

        $materi->delete();
        $this->afterConfigChange($periode, 'ujian_ppi_hafalan_dihapus');

        return redirect()->route('ujianppi.konfigurasi.hafalan', $periode)->with('status', 'Materi setoran dihapus.');
    }

    // ============ helper ============

    protected function editable(PpiExamPeriod $periode): bool
    {
        try {
            $this->service->assertConfigEditable($periode);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function validateScale(Request $request): array
    {
        return array_merge($request->validate([
            'predikat' => ['required', 'string', 'max:10'],
            'nilai_min' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_max' => ['required', 'integer', 'min:0', 'max:100', 'gte:nilai_min'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'is_tidak_lulus' => ['nullable', 'boolean'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]), ['is_tidak_lulus' => $request->boolean('is_tidak_lulus')]);
    }

    protected function assertNoOverlap(PpiExamPeriod $periode, array $data, ?int $exceptId): void
    {
        $duplicateUrutan = PpiExamPredicateScale::where('exam_period_id', $periode->id)
            ->when($exceptId, fn ($q, $id) => $q->where('id', '!=', $id))
            ->where('urutan', $data['urutan'])
            ->exists();

        if ($duplicateUrutan) {
            abort(422, "Urutan {$data['urutan']} sudah dipakai skala lain pada periode ini.");
        }

        $overlap = PpiExamPredicateScale::where('exam_period_id', $periode->id)
            ->when($exceptId, fn ($q, $id) => $q->where('id', '!=', $id))
            ->get()
            ->contains(fn ($scale) => ! ($data['nilai_max'] < $scale->nilai_min || $data['nilai_min'] > $scale->nilai_max));

        if ($overlap) {
            abort(422, 'Rentang nilai skala tidak boleh tumpang tindih dengan skala lain.');
        }
    }

    protected function categoryHasScores(PpiExamAspectCategory $category): bool
    {
        return PpiExamScore::whereIn('aspect_id', $category->aspects()->pluck('id'))->exists();
    }

    protected function afterConfigChange(PpiExamPeriod $periode, string $log): void
    {
        $this->scoring->recomputePeriod($periode);
        activity('akademik')->performedOn($periode)->log($log);
    }
}
