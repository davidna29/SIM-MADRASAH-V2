<?php

namespace App\Http\Controllers\Akademik\UjianPpi;

use App\Http\Controllers\Controller;
use App\Models\PpiExamHafalanScore;
use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamRoom;
use App\Models\PpiExamScore;
use App\Services\PpiExamScoringService;
use App\Services\PpiExamService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruPpiController extends Controller
{
    public function __construct(
        protected PpiExamService $service,
        protected PpiExamScoringService $scoring,
    ) {}

    /**
     * Beranda guru ujian: pilih periode & peran (penguji ruang / pembimbing grup).
     */
    public function index(): View
    {
        $this->authorize('viewAny', PpiExamPeriod::class);

        $user = auth()->user();

        $periods = PpiExamPeriod::with('academicYear')
            ->whereIn('status', [PpiExamPeriod::BERLANGSUNG, PpiExamPeriod::SELESAI])
            ->orderByDesc('id')
            ->get()
            ->filter(function ($period) use ($user) {
                $asExaminer = $this->service->examinerRooms($user, $period)->isNotEmpty();
                $asPembimbing = $this->service->pembimbingGroups($user, $period)->isNotEmpty();

                return $this->service->isAdmin($user) || $asExaminer || $asPembimbing;
            })
            ->map(function ($period) use ($user) {
                $period->peran = collect();
                foreach ($this->service->examinerRooms($user, $period) as $room) {
                    $period->peran->push('Penguji Ruang '.$room->nama);
                }
                foreach ($this->service->pembimbingGroups($user, $period) as $group) {
                    $period->peran->push('Pembimbing Grup '.$group->nama);
                }

                return $period;
            })
            ->values();

        return view('pages.ujianppi.guru.index', [
            'periods' => $periods,
            'service' => $this->service,
        ]);
    }

    // ============ Input Nilai Ujian Lisan ============

    public function ujian(Request $request, PpiExamPeriod $periode): View
    {
        $this->authorize('input', $periode);
        $this->service->assertInputOpen($periode);

        $user = auth()->user();
        $rooms = $this->service->examinerRooms($user, $periode);

        if ($rooms->isEmpty()) {
            abort(403, 'Anda tidak terdaftar sebagai penguji pada periode ini.');
        }

        $room = $this->resolveRoom($rooms, $request->room);
        $urutan = $this->isAdmin($user) ? (int) ($request->urutan ?? 1) : $this->service->examinerUrutan($user, $periode);

        if (! in_array($urutan, [1, 2, 3], true)) {
            abort(403, 'Posisi penguji tidak valid.');
        }

        $categories = $periode->categories()
            ->where('penguji_urutan', $urutan)
            ->with('aspects')
            ->orderBy('urutan')
            ->get();

        $participants = $room->participants()->with('student')->orderBy('no_urut')->get();

        $scores = PpiExamScore::whereIn('participant_id', $participants->pluck('id'))->get()
            ->groupBy('participant_id')
            ->map(fn ($rows) => $rows->keyBy('aspect_id'));

        return view('pages.ujianppi.guru.ujian', [
            'periode' => $periode,
            'rooms' => $rooms,
            'room' => $room,
            'urutan' => $urutan,
            'categories' => $categories,
            'aspects' => $categories->flatMap->aspects,
            'participants' => $participants,
            'scores' => $scores,
            'isAdmin' => $this->isAdmin($user),
        ]);
    }

    public function ujianStore(Request $request, PpiExamPeriod $periode, PpiExamParticipant $peserta): RedirectResponse
    {
        $this->authorize('input', $periode);
        $this->service->assertInputOpen($periode);

        $user = auth()->user();
        $rooms = $this->service->examinerRooms($user, $periode);

        $room = $this->resolveRoom($rooms, (int) $request->room ?: $peserta->exam_room_id);

        if (! $rooms->contains('id', $room->id) || $peserta->exam_room_id !== $room->id) {
            abort(403, 'Peserta tidak berada di ruang ujian Anda.');
        }

        $urutan = $this->isAdmin($user) ? (int) ($request->urutan ?? 1) : $this->service->examinerUrutan($user, $periode);
        if (! in_array($urutan, [1, 2, 3], true)) {
            abort(403, 'Posisi penguji tidak valid.');
        }

        $validated = $request->validate([
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['nullable', 'integer', 'between:0,100'],
        ]);

        // Hanya aspek milik posisi penguji yang boleh diinput
        $allowedAspectIds = $periode->categories()
            ->where('penguji_urutan', $urutan)
            ->with('aspects')
            ->get()
            ->flatMap(fn ($c) => $c->aspects->pluck('id'))
            ->all();

        $examinerEmployeeId = $this->isAdmin($user)
            ? $peserta->room?->examiners()->where('urutan', $urutan)->value('employee_id')
            : $this->service->employeeOfUser($user)?->id;

        if (! $examinerEmployeeId) {
            return back()->withErrors(['nilai' => 'Penguji untuk posisi ini belum ditetapkan di ruang peserta.']);
        }

        $changed = false;
        foreach (($validated['nilai'] ?? []) as $aspectId => $nilai) {
            $aspectId = (int) $aspectId;
            if (! in_array($aspectId, $allowedAspectIds, true)) {
                continue;
            }

            $nilai = ($nilai === '' || $nilai === null) ? null : (int) $nilai;

            if ($nilai === null) {
                PpiExamScore::where('participant_id', $peserta->id)->where('aspect_id', $aspectId)->delete();
                $changed = true;

                continue;
            }

            PpiExamScore::updateOrCreate(
                ['participant_id' => $peserta->id, 'aspect_id' => $aspectId],
                ['nilai' => $nilai, 'examiner_employee_id' => $examinerEmployeeId, 'input_at' => now()]
            );
            $changed = true;
        }

        if (! $changed) {
            return back()->withErrors(['nilai' => 'Tidak ada nilai yang diisi.']);
        }

        $this->scoring->recomputeParticipant($peserta->refresh());
        $this->scoring->recomputeRanks($periode);

        activity('akademik')
            ->performedOn($peserta)
            ->withProperties(['nama' => $peserta->student?->name])
            ->log('ujian_ppi_nilai_ujian_disimpan');

        return redirect()->route('ujianppi.guru.ujian', ['periode' => $periode, 'room' => $room->id, 'urutan' => $urutan])
            ->with('status', 'Nilai ujian lisan disimpan.');
    }

    // ============ Input Nilai Setoran Hafalan ============

    public function setoran(Request $request, PpiExamPeriod $periode): View
    {
        $this->authorize('input', $periode);
        $this->service->assertInputOpen($periode);

        $user = auth()->user();
        $groups = $this->service->pembimbingGroups($user, $periode);

        if ($groups->isEmpty()) {
            abort(403, 'Anda tidak terdaftar sebagai pembimbing setoran pada periode ini.');
        }

        $group = $this->resolveGroup($groups, $request->group);
        $materi = $periode->hafalanMateri()->get();
        $participants = $group->participants()->with('student')->orderBy('no_urut')->get();

        $scores = PpiExamHafalanScore::whereIn('participant_id', $participants->pluck('id'))->get()
            ->groupBy('participant_id')
            ->map(fn ($rows) => $rows->keyBy('hafalan_materi_id'));

        return view('pages.ujianppi.guru.setoran', [
            'periode' => $periode,
            'groups' => $groups,
            'group' => $group,
            'materi' => $materi,
            'participants' => $participants,
            'scores' => $scores,
            'isAdmin' => $this->isAdmin($user),
        ]);
    }

    public function setoranStore(Request $request, PpiExamPeriod $periode, PpiExamParticipant $peserta): RedirectResponse
    {
        $this->authorize('input', $periode);
        $this->service->assertInputOpen($periode);

        $user = auth()->user();
        $groups = $this->service->pembimbingGroups($user, $periode);
        $group = $this->resolveGroup($groups, $request->group);

        if (! $groups->contains('id', $group->id) || $peserta->group_id !== $group->id) {
            abort(403, 'Peserta tidak berada di grup setoran Anda.');
        }

        $validated = $request->validate([
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['nullable', 'integer', 'between:0,100'],
            'tanggal_setor' => ['nullable', 'date'],
        ]);

        $allowedMateriIds = $periode->hafalanMateri()->pluck('id')->all();

        $dinilaiOleh = $this->isAdmin($user)
            ? $group->pembimbing_employee_id
            : $this->service->employeeOfUser($user)?->id;

        if (! $dinilaiOleh) {
            return back()->withErrors(['nilai' => 'Pembimbing grup peserta belum ditetapkan.']);
        }

        $tanggal = $validated['tanggal_setor'] ?? now()->toDateString();

        $changed = false;
        foreach (($validated['nilai'] ?? []) as $materiId => $nilai) {
            $materiId = (int) $materiId;
            if (! in_array($materiId, $allowedMateriIds, true)) {
                continue;
            }

            $nilai = ($nilai === '' || $nilai === null) ? null : (int) $nilai;

            if ($nilai === null) {
                PpiExamHafalanScore::where('participant_id', $peserta->id)->where('hafalan_materi_id', $materiId)->delete();
                $changed = true;

                continue;
            }

            PpiExamHafalanScore::updateOrCreate(
                ['participant_id' => $peserta->id, 'hafalan_materi_id' => $materiId],
                ['nilai' => $nilai, 'dinilai_oleh_employee_id' => $dinilaiOleh, 'tanggal_setor' => $tanggal]
            );
            $changed = true;
        }

        if (! $changed) {
            return back()->withErrors(['nilai' => 'Tidak ada nilai setoran yang diisi.']);
        }

        $this->scoring->recomputeParticipant($peserta->refresh());
        $this->scoring->recomputeRanks($periode);

        activity('akademik')
            ->performedOn($peserta)
            ->withProperties(['nama' => $peserta->student?->name])
            ->log('ujian_ppi_nilai_setoran_disimpan');

        return redirect()->route('ujianppi.guru.setoran', ['periode' => $periode, 'group' => $group->id])
            ->with('status', 'Nilai setoran hafalan disimpan.');
    }

    // ============ Teks Pembawa Acara & Berita Acara ============

    public function teks(Request $request, PpiExamPeriod $periode, PpiExamParticipant $peserta): View
    {
        $this->authorize('input', $periode);

        $user = auth()->user();
        $rooms = $this->service->examinerRooms($user, $periode);

        if (! $this->service->isAdmin($user) && ! $rooms->contains('id', $peserta->exam_room_id)) {
            abort(403, 'Berita acara hanya tersedia untuk penguji ruang peserta.');
        }

        $penutup = (int) $request->penutup ?: 3;
        $vars = $this->service->dokumenVars($peserta, $penutup);

        return view('pages.ujianppi.guru.teks', [
            'periode' => $periode,
            'peserta' => $peserta,
            'penutup' => $penutup,
            'teks_mc' => $this->service->renderTemplate($periode->teks_mc ?: PpiExamService::DEFAULT_TEKS_MC, $vars),
            'teks_ba' => $this->service->renderTemplate($periode->teks_ba ?: PpiExamService::DEFAULT_TEKS_BA, $vars),
            'vars' => $vars,
            'service' => $this->service,
        ]);
    }

    public function teksPdf(PpiExamPeriod $periode, PpiExamParticipant $peserta)
    {
        $this->authorize('input', $periode);

        $user = auth()->user();
        $rooms = $this->service->examinerRooms($user, $periode);

        if (! $this->service->isAdmin($user) && ! $rooms->contains('id', $peserta->exam_room_id)) {
            abort(403, 'Berita acara hanya tersedia untuk penguji ruang peserta.');
        }

        $vars = $this->service->dokumenVars($peserta, 3);
        $teksBa = $this->service->renderTemplate($periode->teks_ba ?: PpiExamService::DEFAULT_TEKS_BA, $vars);

        $filename = 'berita-acara-'.$this->slug($peserta->student?->name).'-'
            .str_replace('/', '-', (string) $periode->academicYear?->name).'.pdf';

        $pdf = Pdf::loadView('pages.ujianppi.guru.teks-pdf', [
            'periode' => $periode,
            'peserta' => $peserta,
            'teks_ba' => $teksBa,
            'kop' => $this->service->kop(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }

    // ============ helper ============

    protected function isAdmin($user): bool
    {
        return $this->service->isAdmin($user);
    }

    protected function resolveRoom($rooms, $roomId): PpiExamRoom
    {
        if ($roomId && $found = $rooms->firstWhere('id', (int) $roomId)) {
            return $found;
        }

        return $rooms->first();
    }

    protected function resolveGroup($groups, $groupId)
    {
        if ($groupId && $found = $groups->firstWhere('id', (int) $groupId)) {
            return $found;
        }

        return $groups->first();
    }

    protected function slug(?string $value): string
    {
        $value = strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? 'siswa';

        return trim($value, '-') ?: 'siswa';
    }
}
