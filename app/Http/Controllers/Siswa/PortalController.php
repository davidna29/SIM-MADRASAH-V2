<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\TuitionOverride;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use App\Support\RingkasanSiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(): View
    {
        $tahun = AcademicYear::active();
        $student = auth()->user()->student;

        $enrollment = $student
            ? StudentEnrollment::with('classGroup')
                ->where('academic_year_id', $tahun->id)
                ->where('student_id', $student->id)
                ->where('status', 'aktif')
                ->first()
            : null;

        return view('pages.siswa.dashboard', [
            'roleLabel' => 'Siswa',
            'breadcrumb' => [['label' => 'Portal Siswa'], ['label' => 'Data Saya']],
            'student' => $student,
            'enrollment' => $enrollment,
            'tahun' => $tahun,
            ...RingkasanSiswa::build($enrollment, $tahun),
        ]);
    }

    public function rapor(): View
    {
        $student = auth()->user()->student;
        abort_unless($student, 404, 'Akun belum terhubung ke data siswa.');

        $report = $student->reports()
            ->where('academic_year_id', AcademicYear::active()->id)
            ->where('semester', AcademicYear::active()->semester)
            ->where('status', 'terbit')
            ->latest()
            ->first();
        abort_unless($report, 404, 'Rapor belum diterbitkan.');

        return view('pages.siswa.rapor', [
            'roleLabel' => 'Siswa',
            'breadcrumb' => [
                ['label' => 'Portal Siswa', 'href' => route('siswa.dashboard')],
                ['label' => 'Rapor Saya'],
            ],
            'student' => $student,
            'report' => $report,
        ]);
    }

    public function raporUnduh()
    {
        $student = auth()->user()->student;
        abort_unless($student, 404);

        $report = $student->reports()
            ->where('academic_year_id', AcademicYear::active()->id)
            ->where('semester', AcademicYear::active()->semester)
            ->where('status', 'terbit')
            ->latest()
            ->first();
        abort_unless($report, 404);

        $pdf = Pdf::loadView('pdf.rapor', ['report' => $report]);
        $tahun = str_replace('/', '-', data_get($report->snapshot, 'tahun'));

        return $pdf->download('rapor-'.$student->nis.'-'.$tahun.'.pdf');
    }

    public function spp(): View
    {
        $tahun = AcademicYear::active();
        $student = auth()->user()->student;
        abort_unless($student, 404, 'Akun belum terhubung ke data siswa.');

        $enrollment = StudentEnrollment::with('classGroup')
            ->where('academic_year_id', $tahun->id)
            ->where('student_id', $student->id)
            ->where('status', 'aktif')
            ->first();

        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);
        $defaultNominal = TuitionSetting::where('academic_year_id', $tahun->id)->value('nominal');
        $overrideNominal = $enrollment
            ? TuitionOverride::where('student_enrollment_id', $enrollment->id)->where('academic_year_id', $tahun->id)->value('nominal')
            : null;

        $payments = collect();
        if ($enrollment) {
            $payments = TuitionPayment::where('student_enrollment_id', $enrollment->id)
                ->where('academic_year_id', $tahun->id)
                ->whereIn('bulan', $months)
                ->get()
                ->keyBy('bulan');
        }

        return view('pages.siswa.spp', [
            'roleLabel' => 'Siswa',
            'breadcrumb' => [
                ['label' => 'Portal Siswa', 'href' => route('siswa.dashboard')],
                ['label' => 'SPP Saya'],
            ],
            'student' => $student,
            'enrollment' => $enrollment,
            'tahun' => $tahun,
            'months' => $months,
            'nominal' => $overrideNominal ?? $defaultNominal,
            'payments' => $payments,
            'semesterMonthsLabel' => collect($months)->mapWithKeys(fn ($bulan) => [$bulan => Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F Y')]),
        ]);
    }
}
