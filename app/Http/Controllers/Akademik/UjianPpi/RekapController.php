<?php

namespace App\Http\Controllers\Akademik\UjianPpi;

use App\Exports\PpiExamRekapExport;
use App\Http\Controllers\Controller;
use App\Models\PpiExamAspect;
use App\Models\PpiExamHafalanScore;
use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamScore;
use App\Services\PpiExamScoringService;
use App\Services\PpiExamService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    public function __construct(
        protected PpiExamService $service,
        protected PpiExamScoringService $scoring,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PpiExamPeriod::class);

        $periods = PpiExamPeriod::with('academicYear')->orderByDesc('id')->get();

        $periode = null;
        $data = null;

        if ($request->periode) {
            $periode = PpiExamPeriod::findOrFail($request->periode);
            $this->authorize('rekapView', $periode);
            $data = $this->rekapData($periode, $request);
        }

        return view('pages.ujianppi.rekap.index', [
            'periods' => $periods,
            'activePeriod' => $periode,
            'data' => $data,
            'service' => $this->service,
        ]);
    }

    public function pdf(Request $request, PpiExamPeriod $periode)
    {
        $this->authorize('rekapView', $periode);

        $data = $this->rekapData($periode, $request);

        $filename = 'rekap-ujian-ppi-'.str_replace('/', '-', (string) $periode->academicYear?->name).'.pdf';

        $pdf = Pdf::loadView('pages.ujianppi.rekap-print', $data + [
            'periode' => $periode,
            'service' => $this->service,
            'kop' => $this->service->kop(),
        ]);

        $pdf->setPaper('A3', 'landscape');

        return $pdf->download($filename);
    }

    public function excel(Request $request, PpiExamPeriod $periode)
    {
        $this->authorize('rekapView', $periode);

        $data = $this->rekapData($periode, $request);

        $filename = 'rekap-ujian-ppi-'.str_replace('/', '-', (string) $periode->academicYear?->name).'.xlsx';

        return Excel::download(new PpiExamRekapExport($data, $periode, $this->service), $filename);
    }

    public function koreksi(Request $request, PpiExamPeriod $periode, PpiExamParticipant $peserta): RedirectResponse
    {
        $this->authorize('rekapEdit', $periode);

        $validated = $request->validate([
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['nullable', 'integer', 'between:0,100'],
            'nilai_hafalan' => ['nullable', 'array'],
            'nilai_hafalan.*' => ['nullable', 'integer', 'between:0,100'],
            'alasan' => ['required', 'string', 'min:5'],
        ]);

        $categories = $periode->categories()->with('aspects')->get();

        $changes = [];

        // Nilai ujian lisan
        $allowedAspectIds = $categories->flatMap(fn ($c) => $c->aspects->pluck('id'))->all();
        foreach (($validated['nilai'] ?? []) as $aspectId => $nilai) {
            $aspectId = (int) $aspectId;
            if (! in_array($aspectId, $allowedAspectIds, true)) {
                continue;
            }
            $nilai = ($nilai === '' || $nilai === null) ? null : (int) $nilai;

            $existing = PpiExamScore::where('participant_id', $peserta->id)->where('aspect_id', $aspectId)->first();
            $old = $existing?->nilai;

            if ($nilai === null) {
                $existing?->delete();
                $changes[] = "aspek #{$aspectId}: ".($old === null ? '—' : $old).' → dihapus';

                continue;
            }

            if ($old === $nilai) {
                continue;
            }

            $examinerEmployeeId = $this->examinerForAspect($peserta, $aspectId);
            if (! $examinerEmployeeId) {
                return back()->withErrors(['nilai' => 'Penguji untuk aspek ini belum ditetapkan di ruang peserta.'])->withInput();
            }

            PpiExamScore::updateOrCreate(
                ['participant_id' => $peserta->id, 'aspect_id' => $aspectId],
                ['nilai' => $nilai, 'examiner_employee_id' => $examinerEmployeeId, 'input_at' => now()]
            );

            $changes[] = "aspek #{$aspectId}: ".($old === null ? '—' : $old)." → {$nilai}";
        }

        // Nilai setoran hafalan
        $materiIds = $periode->hafalanMateri()->pluck('id')->all();
        foreach (($validated['nilai_hafalan'] ?? []) as $materiId => $nilai) {
            $materiId = (int) $materiId;
            if (! in_array($materiId, $materiIds, true)) {
                continue;
            }
            $nilai = ($nilai === '' || $nilai === null) ? null : (int) $nilai;

            $existing = PpiExamHafalanScore::where('participant_id', $peserta->id)->where('hafalan_materi_id', $materiId)->first();
            $old = $existing?->nilai;

            if ($nilai === null) {
                $existing?->delete();
                $changes[] = "hafalan #{$materiId}: ".($old === null ? '—' : $old).' → dihapus';

                continue;
            }

            if ($old === $nilai) {
                continue;
            }

            $dinilaiOleh = $peserta->group?->pembimbing_employee_id ?? $existing?->dinilai_oleh_employee_id;
            if (! $dinilaiOleh) {
                return back()->withErrors(['nilai_hafalan' => 'Pembimbing grup peserta belum ditetapkan.'])->withInput();
            }

            PpiExamHafalanScore::updateOrCreate(
                ['participant_id' => $peserta->id, 'hafalan_materi_id' => $materiId],
                ['nilai' => $nilai, 'dinilai_oleh_employee_id' => $dinilaiOleh, 'tanggal_setor' => $existing?->tanggal_setor ?? now()->toDateString()]
            );

            $changes[] = "hafalan #{$materiId}: ".($old === null ? '—' : $old)." → {$nilai}";
        }

        if (empty($changes)) {
            return back()->withErrors(['alasan' => 'Tidak ada perubahan nilai yang disimpan.'])->withInput();
        }

        $this->scoring->recomputeParticipant($peserta->refresh());
        $this->scoring->recomputeRanks($periode);

        activity('akademik')
            ->performedOn($peserta)
            ->withProperties([
                'alasan' => $validated['alasan'],
                'perubahan' => $changes,
                'nama' => $peserta->student?->name,
            ])
            ->log('ujian_ppi_koreksi_nilai');

        return back()->with('status', 'Koreksi nilai disimpan dan tercatat di audit log.');
    }

    /**
     * Bangun seluruh data tabel Rekap Kelas VI (dengan filter aktif).
     */
    public function rekapData(PpiExamPeriod $periode, Request $request): array
    {
        $categories = $periode->categories()->with('aspects')->orderBy('penguji_urutan')->orderBy('urutan')->get();
        $hafalanMateri = $periode->hafalanMateri()->get();

        $query = $periode->participants()->with(['student', 'room', 'group', 'predicateScale', 'classGroup']);

        if ($request->room_id) {
            $query->where('exam_room_id', $request->room_id);
        }
        if ($request->group_id) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->lulus === 'lulus') {
            $query->where('status_lulus', true);
        } elseif ($request->lulus === 'tidak') {
            $query->where('status_lulus', false);
        }
        if ($request->class_group_id) {
            $query->where('class_group_id', $request->class_group_id);
        }
        if ($request->q) {
            $q = $request->q;
            $query->whereHas('student', fn ($s) => $s->where('name', 'like', "%{$q}%")->orWhere('nis', 'like', "%{$q}%"));
        }

        $participants = $query->orderBy('no_urut')->get();

        $participantIds = $participants->pluck('id');

        $scores = PpiExamScore::whereIn('participant_id', $participantIds)->get()
            ->groupBy('participant_id')
            ->map(fn ($rows) => $rows->keyBy('aspect_id'));

        $hafalanScores = PpiExamHafalanScore::whereIn('participant_id', $participantIds)->get()
            ->groupBy('participant_id')
            ->map(fn ($rows) => $rows->keyBy('hafalan_materi_id'));

        return [
            'categories' => $categories,
            'hafalanMateri' => $hafalanMateri,
            'participants' => $participants,
            'scores' => $scores,
            'hafalanScores' => $hafalanScores,
        ];
    }

    protected function examinerForAspect(PpiExamParticipant $peserta, int $aspectId): ?int
    {
        $aspect = PpiExamAspect::with('category')->find($aspectId);
        $urutan = $aspect?->category?->penguji_urutan;

        if (! $urutan) {
            return null;
        }

        return $peserta->room?->examiners()->where('urutan', $urutan)->value('employee_id');
    }
}
