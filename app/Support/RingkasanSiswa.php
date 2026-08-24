<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use Carbon\Carbon;

class RingkasanSiswa
{
    /**
     * Agregat read-only untuk satu siswa pada tahun ajaran aktif:
     * nilai/rapor (items), kehadiran bulanan H/S/I/A, dan ringkasan SPP.
     */
    public static function build(?StudentEnrollment $enrollment, AcademicYear $tahun): array
    {
        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);

        $report = null;
        $items = collect();

        if ($enrollment) {
            $report = $enrollment->student->reports()
                ->where('academic_year_id', $tahun->id)
                ->where('semester', $tahun->semester)
                ->where('status', 'terbit')
                ->latest()
                ->first();

            $items = $report?->subjectItems() ?? collect();
        }

        $attendanceByMonth = Attendance::where('student_enrollment_id', $enrollment?->id)
            ->get()
            ->groupBy(fn ($a) => (int) $a->attendance_date->format('n'));

        $kehadiran = collect($months)->map(function ($bulan) use ($attendanceByMonth) {
            $rows = $attendanceByMonth->get($bulan, collect());

            return [
                'bulan' => $bulan,
                'H' => $rows->where('status', 'hadir')->count(),
                'S' => $rows->where('status', 'sakit')->count(),
                'I' => $rows->where('status', 'izin')->count(),
                'A' => $rows->where('status', 'alpha')->count(),
            ];
        });

        $spp = TuitionPayment::where('student_enrollment_id', $enrollment?->id)
            ->whereIn('bulan', $months)
            ->get()
            ->keyBy('bulan');
        $sppLunas = $spp->where('status', 'lunas')->count();
        $sppTerakhir = $spp->where('status', 'lunas')->sortByDesc('bulan')->first();

        return [
            'report' => $report,
            'items' => $items,
            'months' => $months,
            'monthsLabel' => collect($months)->mapWithKeys(fn ($bulan) => [$bulan => Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F')]),
            'kehadiran' => $kehadiran,
            'spp' => $spp,
            'sppLunas' => $sppLunas,
            'sppTotal' => count($months),
            'sppTerakhir' => $sppTerakhir,
        ];
    }
}
