<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\ExtracurricularAttendance;
use App\Models\ExtracurricularMember;
use App\Models\Offense;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use Carbon\Carbon;

class PortofolioService
{
    /**
     * Agregasi portofolio lengkap untuk satu siswa pada tahun ajaran aktif.
     * Read-only — tidak ada data baru yang dibuat.
     */
    public static function build(Student $student, AcademicYear $tahun): array
    {
        // Enrollment aktif (atau terakhir jika tidak ada yang aktif)
        $enrollment = StudentEnrollment::with('classGroup')
            ->where('student_id', $student->id)
            ->where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->first()
            ?? $student->enrollments()
                ->with('classGroup')
                ->where('academic_year_id', $tahun->id)
                ->latest()
                ->first();

        $enrollmentId = $enrollment?->id;

        // 1. Rapor + Nilai
        $report = null;
        $raporItems = collect();
        if ($enrollmentId) {
            $report = $student->reports()
                ->where('academic_year_id', $tahun->id)
                ->where('semester', $tahun->semester)
                ->where('status', 'terbit')
                ->latest()
                ->first();
            $raporItems = $report?->subjectItems() ?? collect();
        }

        // 2. Kehadiran bulanan
        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);
        $attendanceByMonth = Attendance::where('student_enrollment_id', $enrollmentId)
            ->get()
            ->groupBy(fn ($a) => (int) $a->attendance_date->format('n'));

        $kehadiran = collect($months)->map(function ($bulan) use ($attendanceByMonth) {
            $rows = $attendanceByMonth->get($bulan, collect());

            return [
                'bulan' => $bulan,
                'label' => Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F'),
                'H' => $rows->where('status', 'hadir')->count(),
                'S' => $rows->where('status', 'sakit')->count(),
                'I' => $rows->where('status', 'izin')->count(),
                'A' => $rows->where('status', 'alpha')->count(),
            ];
        });

        $totalHadir = $kehadiran->sum('H');
        $totalSakit = $kehadiran->sum('S');
        $totalIzin = $kehadiran->sum('I');
        $totalAlpha = $kehadiran->sum('A');
        $totalKehadiran = $totalHadir + $totalSakit + $totalIzin + $totalAlpha;

        // 3. Prestasi (terverifikasi) — relasi via student_id, bukan enrollment
        $prestasi = Achievement::where('student_id', $student->id)
            ->where('status_verifikasi', 'terverifikasi')
            ->orderByDesc('tanggal')
            ->get();

        // 4. Pelanggaran (selesai) — relasi via student_id, bukan enrollment
        $pelanggaran = Offense::where('student_id', $student->id)
            ->where('status_penyelesaian', 'selesai')
            ->orderByDesc('tanggal_kejadian')
            ->get();
        $totalPoin = $pelanggaran->sum('poin');

        // 5. Ekstrakurikuler
        $ekskulMembers = $enrollmentId
            ? ExtracurricularMember::with('ekskul')
                ->where('student_enrollment_id', $enrollmentId)
                ->get()
            : collect();

        $ekskulRata = $ekskulMembers->map(function ($member) {
            $attendances = ExtracurricularAttendance::where('student_enrollment_id', $member->student_enrollment_id)
                ->where('extracurricular_id', $member->extracurricular_id)
                ->get();
            $avg = $attendances->avg('poin');
            $predikat = ExtracurricularAttendance::predicateFromAverage($avg ?? 0);

            return [
                'nama' => $member->ekskul->name,
                'predikat' => $predikat,
                'rata_poin' => round($avg ?? 0, 1),
            ];
        });

        // 6. SPP
        $spp = TuitionPayment::where('student_enrollment_id', $enrollmentId)
            ->whereIn('bulan', $months)
            ->get()
            ->keyBy('bulan');
        $sppLunas = $spp->where('status', 'lunas')->count();

        return [
            'student' => $student,
            'enrollment' => $enrollment,
            'kelas' => $enrollment?->classGroup?->name ?? '–',
            'report' => $report,
            'raporItems' => $raporItems,
            'months' => $months,
            'kehadiran' => $kehadiran,
            'totalHadir' => $totalHadir,
            'totalSakit' => $totalSakit,
            'totalIzin' => $totalIzin,
            'totalAlpha' => $totalAlpha,
            'totalKehadiran' => $totalKehadiran,
            'persentaseHadir' => $totalKehadiran > 0 ? round(($totalHadir / $totalKehadiran) * 100, 1) : 0,
            'prestasi' => $prestasi,
            'pelanggaran' => $pelanggaran,
            'totalPoinPelanggaran' => $totalPoin,
            'ekskul' => $ekskulRata,
            'spp' => $spp,
            'sppLunas' => $sppLunas,
            'sppTotal' => count($months),
        ];
    }
}
