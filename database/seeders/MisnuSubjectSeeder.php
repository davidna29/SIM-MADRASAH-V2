<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;

class MisnuSubjectSeeder extends Seeder
{
    /**
     * Memetakan sepuluh mapel generik hasil skeleton ke kurikulum MIS NU agar
     * penugasan/nilai demo tidak kehilangan relasi.
     *
     * @var array<string, string>
     */
    private const LEGACY = [
        'MAT' => 'MAT',
        'BIN' => 'BIN',
        'BING' => 'BIG',
        'IPA' => 'IPAS',
        'IPS' => 'ASW',
        'PAI' => 'AQA',
        'SBK' => 'SBDP',
        'PJOK' => 'PJOK',
        'PKN' => 'PPK',
        'BTA' => 'QUR',
    ];

    public function run(): void
    {
        foreach (self::LEGACY as $old => $new) {
            $subject = Subject::where('code', $old)->first();

            if (! $subject || $old === $new) {
                continue;
            }

            if (Subject::where('code', $new)->whereKeyNot($subject->id)->exists()) {
                continue;
            }

            $subject->update(['code' => $new]);
        }

        foreach (MisnuReference::subjects() as $position => $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                [
                    'name' => $subject['name'],
                    'category' => $subject['category'],
                    'jp_per_grade' => $subject['jp'],
                    'sort_order' => $position + 1,
                ]
            );
        }
    }
}
