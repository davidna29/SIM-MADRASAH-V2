<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $students = auth()->user()->guardian?->students()->with('reports')->get() ?? collect();

        return view('pages.ortu.dashboard', [
            'roleLabel' => 'Orang Tua / Wali',
            'breadcrumb' => [['label' => 'Portal Orang Tua'], ['label' => 'Anak Saya']],
            'students' => $students,
        ]);
    }

    public function ringkasan(Student $student): View
    {
        abort_unless($this->owns($student), 403);

        $tahun = AcademicYear::active();
        $enrollment = StudentEnrollment::with('classGroup')
            ->where('academic_year_id', $tahun->id)
            ->where('student_id', $student->id)
            ->where('status', 'aktif')
            ->first();

        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);

        $report = $student->reports()
            ->where('academic_year_id', $tahun->id)
            ->where('semester', $tahun->semester)
            ->where('status', 'terbit')
            ->latest()
            ->first();
        $items = $report?->subjectItems() ?? collect();

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
        $sppTotal = count($months);
        $sppTerakhir = $spp->where('status', 'lunas')->sortByDesc('bulan')->first();

        return view('pages.ortu.ringkasan', [
            'roleLabel' => 'Orang Tua / Wali',
            'breadcrumb' => [
                ['label' => 'Portal Orang Tua', 'href' => route('ortu.dashboard')],
                ['label' => 'Ringkasan '.$student->name],
            ],
            'student' => $student,
            'enrollment' => $enrollment,
            'tahun' => $tahun,
            'months' => $months,
            'monthsLabel' => collect($months)->mapWithKeys(fn ($bulan) => [$bulan => Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F')]),
            'items' => $items,
            'report' => $report,
            'kehadiran' => $kehadiran,
            'spp' => $spp,
            'sppLunas' => $sppLunas,
            'sppTotal' => $sppTotal,
            'sppTerakhir' => $sppTerakhir,
        ]);
    }

    public function rapor(Student $student): View
    {
        abort_unless($this->owns($student), 403);

        $report = $student->reports()->where('status', 'terbit')->latest()->first();

        if (! $report) {
            abort(404, 'Rapor anak belum diterbitkan.');
        }

        return view('pages.ortu.rapor', [
            'roleLabel' => 'Orang Tua / Wali',
            'breadcrumb' => [
                ['label' => 'Portal Orang Tua', 'href' => route('ortu.dashboard')],
                ['label' => 'Rapor '.$student->name],
            ],
            'student' => $student,
            'report' => $report,
        ]);
    }

    public function unduh(Student $student)
    {
        abort_unless($this->owns($student), 403);

        $report = $student->reports()->where('status', 'terbit')->latest()->first();

        abort_unless($report, 404);

        $pdf = Pdf::loadView('pdf.rapor', ['report' => $report]);
        $tahun = str_replace('/', '-', data_get($report->snapshot, 'tahun'));

        return $pdf->download('rapor-'.$student->nis.'-'.$tahun.'.pdf');
    }

    protected function owns(Student $student): bool
    {
        return auth()->user()->guardian?->students()->whereKey($student->id)->exists() ?? false;
    }
}
