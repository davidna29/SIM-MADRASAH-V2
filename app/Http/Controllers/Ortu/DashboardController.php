<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Student;
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rapor', ['report' => $report]);
        $tahun = str_replace('/', '-', data_get($report->snapshot, 'tahun'));

        return $pdf->download('rapor-'.$student->nis.'-'.$tahun.'.pdf');
    }

    protected function owns(Student $student): bool
    {
        return auth()->user()->guardian?->students()->whereKey($student->id)->exists() ?? false;
    }
}
