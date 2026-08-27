<?php

namespace App\Services;

use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamPredicateScale;
use Illuminate\Support\Facades\DB;

class PpiExamScoringService
{
    /**
     * Hitung ulang seluruh angka cache satu peserta (jumlah/rata per penguji,
     * rata hafalan, nilai akhir sesuai bobot, predikat & status lulus).
     */
    public function recomputeParticipant(PpiExamParticipant $participant): void
    {
        $period = $participant->period;

        // Aspek dikelompokkan per nomor penguji (1/2/3)
        $categoryAspectIds = $period->categories()
            ->with('aspects')
            ->get()
            ->groupBy('penguji_urutan')
            ->map(fn ($categories) => $categories->flatMap(fn ($c) => $c->aspects->pluck('id'))->all());

        $aspectIds = $categoryAspectIds->flatten()->all();

        $scores = $participant->scores()
            ->whereIn('aspect_id', $aspectIds)
            ->get()
            ->keyBy('aspect_id');

        $sums = [];
        $counts = [];
        foreach ([1, 2, 3] as $urutan) {
            $ids = $categoryAspectIds[$urutan] ?? [];
            $sum = 0;
            $count = 0;
            foreach ($ids as $aspectId) {
                if ($scores->has($aspectId)) {
                    $sum += $scores[$aspectId]->nilai;
                    $count++;
                }
            }
            $sums[$urutan] = $sum;
            $counts[$urutan] = $count;
        }

        $hafalan = $participant->hafalanScores()->get();
        $hafalanSum = $hafalan->sum('nilai');
        $hafalanCount = $hafalan->count();

        $rata = fn (int $u) => $counts[$u] > 0 ? round($sums[$u] / $counts[$u], 2) : null;
        $rataP1 = $rata(1);
        $rataP2 = $rata(2);
        $rataP3 = $rata(3);
        $rataHafalan = $hafalanCount > 0 ? round($hafalanSum / $hafalanCount, 2) : null;

        // Rata gabungan ujian lisan (ketiga penguji wajib lengkap)
        $rataUjianLisan = null;
        if ($rataP1 !== null && $rataP2 !== null && $rataP3 !== null) {
            $rataUjianLisan = round(($rataP1 + $rataP2 + $rataP3) / 3, 2);
        }

        // Nilai akhir = Σ(rata × bobot/100); komponen berbobot >0 wajib lengkap
        [$nilaiAkhir, $complete] = $this->nilaiAkhir(
            $period,
            ['p1' => $rataP1, 'p2' => $rataP2, 'p3' => $rataP3, 'hafalan' => $rataHafalan]
        );

        if (! $complete) {
            $nilaiAkhir = null;
        }

        // Predikat & status lulus dari skala periode
        $predicateScaleId = null;
        $statusLulus = null;
        if ($nilaiAkhir !== null) {
            $scale = PpiExamPredicateScale::where('exam_period_id', $period->id)
                ->where('nilai_min', '<=', $nilaiAkhir)
                ->where('nilai_max', '>=', $nilaiAkhir)
                ->first();
            $predicateScaleId = $scale?->id;
            $statusLulus = $scale ? ! $scale->is_tidak_lulus : null;
        }

        $participant->update([
            'jumlah_p1' => $counts[1] > 0 ? $sums[1] : null,
            'rata_p1' => $rataP1,
            'jumlah_p2' => $counts[2] > 0 ? $sums[2] : null,
            'rata_p2' => $rataP2,
            'jumlah_p3' => $counts[3] > 0 ? $sums[3] : null,
            'rata_p3' => $rataP3,
            'jumlah_ujian_lisan' => $rataUjianLisan !== null ? array_sum($sums) : null,
            'rata_ujian_lisan' => $rataUjianLisan,
            'rata_hafalan' => $rataHafalan,
            'nilai_akhir' => $nilaiAkhir,
            'predicate_scale_id' => $predicateScaleId,
            'status_lulus' => $statusLulus,
        ]);
    }

    /**
     * Hitung nilai akhir sesuai bobot. Wajib lengkap untuk komponen berbobot > 0.
     *
     * @return array{0: float|null, 1: bool} [nilai, lengkap]
     */
    protected function nilaiAkhir(PpiExamPeriod $period, array $rata): array
    {
        $weights = [
            'p1' => $period->bobot_p1,
            'p2' => $period->bobot_p2,
            'p3' => $period->bobot_p3,
            'hafalan' => $period->bobot_hafalan,
        ];

        $total = 0.0;

        foreach ($weights as $key => $weight) {
            if ($weight <= 0) {
                continue; // komponen tidak dipakai — diabaikan
            }
            if ($rata[$key] === null) {
                return [null, false];
            }
            $total += $rata[$key] * ($weight / 100);
        }

        return [round($total, 2), true];
    }

    /**
     * Hitung ulang semua peserta lalu Rank Total (lintas ruang) & Rank Lokal (per rombel).
     */
    public function recomputePeriod(PpiExamPeriod $period): void
    {
        $participants = $period->participants()->get();

        foreach ($participants as $participant) {
            if (! $participant->relationLoaded('period')) {
                $participant->setRelation('period', $period);
            }
            $this->recomputeParticipant($participant);
        }

        $this->recomputeRanks($period);
    }

    /**
     * Rank hanya untuk peserta dengan nilai akhir lengkap.
     */
    public function recomputeRanks(PpiExamPeriod $period): void
    {
        $ranked = $period->participants()
            ->whereNotNull('nilai_akhir')
            ->orderByDesc('nilai_akhir')
            ->orderBy('no_urut')
            ->get();

        $rankMap = [];
        $lokalMap = [];
        foreach ($ranked as $i => $participant) {
            $rankMap[$participant->id] = $i + 1;
            $lokalMap[$participant->class_group_id][$participant->id] = count($lokalMap[$participant->class_group_id] ?? []) + 1;
        }

        $ids = array_unique(array_merge(
            array_keys($rankMap),
            collect($lokalMap)->flatMap(fn ($m) => array_keys($m))->all(),
        ));

        $participants = $period->participants()->whereIn('id', $ids)->get();

        DB::transaction(function () use ($participants, $rankMap, $lokalMap) {
            foreach ($participants as $participant) {
                $participant->update([
                    'rank_total' => $rankMap[$participant->id] ?? null,
                    'rank_lokal' => $lokalMap[$participant->class_group_id][$participant->id] ?? null,
                ]);
            }
        });
    }
}
