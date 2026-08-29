<?php

namespace App\Support;

use Illuminate\Support\Collection;

class Nilai
{
    /**
     * Hitung nilai akhir (0-100) dari komponen berbobot.
     *
     * @param  Collection<int, array{value: int|null, weight: float}>  $items
     */
    public static function finalScore(Collection $items): ?int
    {
        $weighted = $items->filter(fn ($item) => $item['value'] !== null);

        if ($weighted->isEmpty()) {
            return null;
        }

        $totalWeight = (float) $weighted->sum('weight');

        if ($totalWeight <= 0) {
            return null;
        }

        return (int) round($weighted->sum(fn ($item) => $item['value'] * $item['weight']) / $totalWeight);
    }

    public static function predikat(int $score): string
    {
        return Rapor::predikat($score);
    }
}
