<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\TuitionOverride;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use Carbon\Carbon;
use Illuminate\View\View;

class SppController extends Controller
{
    public function index(): View
    {
        $students = auth()->user()->guardian?->students()->get() ?? collect();

        return view('pages.ortu.spp.index', [
            'roleLabel' => 'Orang Tua / Wali',
            'breadcrumb' => [['label' => 'Portal Orang Tua'], ['label' => 'SPP Anak']],
            'students' => $students,
        ]);
    }

    public function show(Student $student): View
    {
        abort_unless($this->owns($student), 403);

        $tahun = AcademicYear::active();
        $enrollment = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('student_id', $student->id)
            ->where('status', 'aktif')
            ->first();

        $defaultNominal = TuitionSetting::where('academic_year_id', $tahun->id)->value('nominal');
        $overrideNominal = $enrollment
            ? TuitionOverride::where('student_enrollment_id', $enrollment->id)->where('academic_year_id', $tahun->id)->value('nominal')
            : null;

        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);
        $payments = collect();

        if ($enrollment) {
            $payments = TuitionPayment::where('student_enrollment_id', $enrollment->id)
                ->where('academic_year_id', $tahun->id)
                ->whereIn('bulan', $months)
                ->get()
                ->keyBy('bulan');
        }

        return view('pages.ortu.spp.show', [
            'roleLabel' => 'Orang Tua / Wali',
            'breadcrumb' => [
                ['label' => 'Portal Orang Tua', 'href' => route('ortu.dashboard')],
                ['label' => 'SPP Anak', 'href' => route('ortu.spp.index')],
                ['label' => $student->name],
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

    protected function owns(Student $student): bool
    {
        return auth()->user()->guardian?->students()->whereKey($student->id)->exists() ?? false;
    }
}
