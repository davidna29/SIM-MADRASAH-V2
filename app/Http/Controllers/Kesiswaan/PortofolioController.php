<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Support\PortofolioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PortofolioController extends Controller
{
    /**
     * Form pencarian siswa untuk portofolio.
     */
    public function index(): View
    {
        Gate::authorize('portfolio.viewAny');

        $search = request('q');
        $students = $search
            ? Student::where('nis', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->with('enrollments.classGroup')
                ->limit(20)
                ->get()
            : collect();

        return view('pages.kesiswaan.portofolio.index', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Portofolio Digital'],
            ],
            'students' => $students,
            'search' => $search,
        ]);
    }

    /**
     * Tampilkan portofolio lengkap siswa.
     */
    public function show(Student $student): View
    {
        Gate::authorize('portfolio.view', $student);

        $tahun = AcademicYear::active();
        $portofolio = PortofolioService::build($student, $tahun);

        return view('pages.kesiswaan.portofolio.show', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Portofolio Digital', 'href' => route('portofolio.index')],
                ['label' => $student->name],
            ],
            'portofolio' => $portofolio,
            'student' => $student,
        ]);
    }

    /**
     * Generate & tampilkan QR Code portofolio.
     */
    public function qr(Student $student)
    {
        Gate::authorize('portfolio.view', $student);

        $token = Crypt::encryptString($student->id.'|'.time());
        $url = route('portofolio.verify', $token);

        $qr = \QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($url);

        return response($qr)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="portofolio-'.$student->nis.'.png"');
    }

    /**
     * Cetak portofolio sebagai PDF.
     */
    public function print(Student $student)
    {
        Gate::authorize('portfolio.view', $student);

        $tahun = AcademicYear::active();
        $portofolio = PortofolioService::build($student, $tahun);

        $pdf = Pdf::loadView('pages.kesiswaan.portofolio.print', [
            'portofolio' => $portofolio,
            'student' => $student,
        ])->setPaper('a4', 'portrait');

        $filename = 'portofolio-'.$student->nis.'-'.$tahun->name.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Verifikasi portofolio via token publik (harus login).
     */
    public function verify(Request $request, string $token): View|RedirectResponse
    {
        Gate::authorize('portfolio.viewAny');

        try {
            $decrypted = Crypt::decryptString($token);
            [$studentId, $timestamp] = explode('|', $decrypted);

            // Token valid selama 30 hari
            if (time() - (int) $timestamp > 30 * 24 * 3600) {
                abort(410, 'Token portofolio sudah kedaluwarsa.');
            }

            $student = Student::findOrFail($studentId);
        } catch (\Exception $e) {
            abort(403, 'Token portofolio tidak valid.');
        }

        $tahun = AcademicYear::active();
        $portofolio = PortofolioService::build($student, $tahun);

        return view('pages.kesiswaan.portofolio.verify', [
            'portofolio' => $portofolio,
            'student' => $student,
        ]);
    }
}
