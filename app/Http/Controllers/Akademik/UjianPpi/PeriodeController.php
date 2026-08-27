<?php

namespace App\Http\Controllers\Akademik\UjianPpi;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\PpiExamPeriod;
use App\Services\PpiExamScoringService;
use App\Services\PpiExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodeController extends Controller
{
    public function __construct(
        protected PpiExamService $service,
        protected PpiExamScoringService $scoring,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PpiExamPeriod::class);

        $periods = PpiExamPeriod::with('academicYear')
            ->withCount(['participants', 'rooms'])
            ->when($request->academic_year_id, fn ($q, $id) => $q->where('academic_year_id', $id))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pages.ujianppi.periode.index', [
            'periods' => $periods,
            'years' => AcademicYear::orderByDesc('id')->get(),
            'navPeriode' => $request->academic_year_id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', PpiExamPeriod::class);

        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'judul' => ['required', 'string', 'max:150'],
            'tanggal_setoran_mulai' => ['nullable', 'date'],
            'tanggal_setoran_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_setoran_mulai'],
            'tanggal_ujian' => ['nullable', 'date'],
        ]);

        $period = PpiExamPeriod::create([
            'academic_year_id' => $validated['academic_year_id'],
            'judul' => $validated['judul'],
            'tanggal_setoran_mulai' => $validated['tanggal_setoran_mulai'] ?? null,
            'tanggal_setoran_selesai' => $validated['tanggal_setoran_selesai'] ?? null,
            'tanggal_ujian' => $validated['tanggal_ujian'] ?? null,
            'status' => PpiExamPeriod::DRAFT,
            'bobot_p1' => 25,
            'bobot_p2' => 25,
            'bobot_p3' => 25,
            'bobot_hafalan' => 25,
            'teks_mc' => PpiExamService::DEFAULT_TEKS_MC,
            'teks_ba' => PpiExamService::DEFAULT_TEKS_BA,
        ]);

        // Isi struktur aspek & materi default (§6 Data Default)
        $this->service->seedDefaults($period);

        activity('akademik')
            ->performedOn($period)
            ->withProperties(['judul' => $period->judul])
            ->log('ujian_ppi_periode_dibuat');

        return redirect()->route('ujianppi.periode.show', $period)
            ->with('status', 'Periode ujian dibuat. Lengkapi konfigurasi di menu Skala, Bobot, Aspek, dan Materi.');
    }

    public function show(PpiExamPeriod $periode): View
    {
        $this->authorize('view', $periode);

        $periode->load([
            'academicYear',
            'scales' => fn ($q) => $q->orderBy('urutan'),
            'categories.aspects',
            'hafalanMateri',
            'rooms.examiners.employee.person',
            'groups.pembimbing.person',
            'participants' => fn ($q) => $q->with(['student', 'room', 'group'])->orderBy('no_urut'),
        ]);

        return view('pages.ujianppi.periode.show', [
            'periode' => $periode,
            'service' => $this->service,
        ]);
    }

    public function update(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'tanggal_setoran_mulai' => ['nullable', 'date'],
            'tanggal_setoran_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_setoran_mulai'],
            'tanggal_ujian' => ['nullable', 'date'],
        ]);

        $periode->update($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_periode_diubah');

        return redirect()->route('ujianppi.periode.show', $periode)->with('status', 'Periode ujian diperbarui.');
    }

    public function destroy(PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);

        if (! in_array($periode->status, [PpiExamPeriod::DRAFT, PpiExamPeriod::SETUP], true)) {
            return back()->withErrors(['period' => 'Periode yang sudah berlangsung tidak bisa dihapus.']);
        }

        $judul = $periode->judul;
        $periode->delete();

        activity('akademik')->withProperties(['judul' => $judul])->log('ujian_ppi_periode_dihapus');

        return redirect()->route('ujianppi.periode.index')->with('status', 'Periode ujian dihapus.');
    }

    public function status(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);

        $target = (string) $request->input('status');

        if (! $periode->canTransitionTo($target)) {
            return back()->withErrors(['status' => 'Transisi status tidak valid.']);
        }

        if ($target === PpiExamPeriod::BERLANGSUNG) {
            if (! $periode->bobotValid()) {
                return back()->withErrors(['status' => 'Bobot penilaian harus berjumlah 100% sebelum periode berlangsung.']);
            }
            if ($periode->scales()->count() === 0) {
                return back()->withErrors(['status' => 'Tetapkan minimal satu skala predikat sebelum periode berlangsung.']);
            }
            if ($periode->categories()->count() === 0 || $periode->hafalanMateri()->count() === 0) {
                return back()->withErrors(['status' => 'Lengkapi struktur aspek dan materi setoran sebelum periode berlangsung.']);
            }
            $periode->update(['status' => $target, 'config_locked_at' => now()]);
        } elseif ($target === PpiExamPeriod::SETUP && $periode->status === PpiExamPeriod::BERLANGSUNG) {
            // Mundur ke setup hanya bila belum ada nilai sama sekali
            $hasScores = $periode->participants()
                ->where(fn ($q) => $q->whereHas('scores')->orWhereHas('hafalanScores'))
                ->exists();
            if ($hasScores) {
                return back()->withErrors(['status' => 'Tidak bisa mundur ke Setup: sudah ada nilai yang tersimpan.']);
            }
            $periode->update(['status' => $target, 'config_locked_at' => null]);
        } else {
            $periode->update(['status' => $target]);
        }

        activity('akademik')
            ->performedOn($periode)
            ->withProperties(['status_baru' => $periode->statusLabel()])
            ->log('ujian_ppi_periode_status');

        return back()->with('status', 'Status periode diubah menjadi '.$periode->statusLabel().'.');
    }

    /**
     * Salin skala predikat dari periode sebelumnya (periode aktif lain dengan skala).
     */
    public function copyScales(PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $source = PpiExamPeriod::where('id', '!=', $periode->id)
            ->whereHas('scales')
            ->orderByDesc('id')
            ->first();

        if (! $source) {
            return back()->withErrors(['scales' => 'Tidak ada periode sebelumnya dengan skala untuk disalin.']);
        }

        $periode->scales()->delete();

        foreach ($source->scales as $scale) {
            $periode->scales()->create([
                'predikat' => $scale->predikat,
                'nilai_min' => $scale->nilai_min,
                'nilai_max' => $scale->nilai_max,
                'deskripsi' => $scale->deskripsi,
                'is_tidak_lulus' => $scale->is_tidak_lulus,
                'urutan' => $scale->urutan,
            ]);
        }

        activity('akademik')->performedOn($periode)->log('ujian_ppi_skala_disalin');

        return redirect()->route('ujianppi.konfigurasi.skala', $periode)
            ->with('status', 'Skala predikat disalin dari "'.$source->judul.'".');
    }

    public function kunci(PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('unlock', $periode);

        $periode->update(['config_locked_at' => now()]);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_konfigurasi_dikunci');

        return back()->with('status', 'Konfigurasi periode dikunci kembali.');
    }

    public function bukaKunci(PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('unlock', $periode);

        $periode->update(['config_locked_at' => null]);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_konfigurasi_dibuka_kunci');

        return back()->with('status', 'Konfigurasi periode dibuka kunci (Super Admin). Perubahan tercatat di audit log.');
    }
}
