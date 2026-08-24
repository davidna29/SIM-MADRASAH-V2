<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill 1: salin detail mapel dari snapshot single-mapel lama ke report_items
        // agar data rapor MAT I-A (atau rapor lain yang sudah terbit) tidak hilang.
        DB::table('reports')->orderBy('id')->get()->each(function ($report) {
            $snapshot = is_string($report->snapshot) ? json_decode($report->snapshot, true) : $report->snapshot;
            $snapshot = is_array($snapshot) ? $snapshot : [];

            if (empty($snapshot['mapel'])) {
                return;
            }

            DB::table('report_items')->insertOrIgnore([
                'report_id' => $report->id,
                'subject_code' => $snapshot['kode_mapel'] ?? '',
                'subject_name' => $snapshot['mapel'] ?? '',
                'class_group_id' => null,
                'class_name' => $snapshot['kelas'] ?? '',
                'teacher_name' => $snapshot['guru'] ?? '',
                'score' => $snapshot['score'] ?? null,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Backfill 2: konsolidasi rapor terfragmentasi (1 baris per mapel) menjadi
        // 1 rapor parent per (siswa, tahun, semester) — item dipindah, bukan dihapus.
        DB::table('reports')->orderBy('id')->get()
            ->groupBy(fn ($r) => $r->student_id.'|'.$r->academic_year_id.'|'.$r->semester)
            ->each(function ($group) {
                $parent = $group->first();
                $siblings = $group->slice(1);

                foreach ($siblings as $sib) {
                    DB::table('report_items')->where('report_id', $sib->id)->update(['report_id' => $parent->id]);
                    DB::table('reports')->where('id', $sib->id)->delete();
                }

                $legacy = is_string($parent->snapshot) ? json_decode($parent->snapshot, true) : $parent->snapshot;
                $legacy = is_array($legacy) ? $legacy : [];
                $student = DB::table('students')->where('id', $parent->student_id)->first();
                $kelas = DB::table('report_items')->where('report_id', $parent->id)->value('class_name');

                DB::table('reports')->where('id', $parent->id)->update([
                    'snapshot' => json_encode([
                        'tahun' => $legacy['tahun'] ?? '',
                        'semester' => $parent->semester,
                        'nis' => $student->nis ?? '',
                        'siswa' => $student->name ?? '',
                        'kelas' => $kelas ?? ($legacy['kelas'] ?? ''),
                        'terbit_pada' => $legacy['terbit_pada'] ?? now()->toDateTimeString(),
                    ]),
                    'status' => 'terbit',
                ]);
            });

        // Kembalikan invariant basis data: satu rapor per siswa+tahun+semester.
        Schema::table('reports', function (Blueprint $table) {
            $table->unique(['student_id', 'academic_year_id', 'semester'], 'report_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('report_unique');
        });
    }
};
