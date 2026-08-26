<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Http\Requests\PpdbRegistrationRequest;
use App\Models\AcademicYear;
use App\Models\PpdbRegistration;
use Illuminate\View\View;

class PpdbController extends Controller
{
    public function index(): View
    {
        $registration = null;

        // If user has a registration number in session, load it
        if (session('ppdb_registration_no')) {
            $registration = PpdbRegistration::where('registration_no', session('ppdb_registration_no'))->first();
        }

        return view('pages.publik.ppdb.index', [
            'registration' => $registration,
            'tahun' => AcademicYear::active(),
        ]);
    }

    public function store(PpdbRegistrationRequest $request)
    {
        $validated = $request->validated();

        // Cast booleans
        $booleanFields = ['dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual',
            'dis_behavioral', 'dis_slow_learner', 'dis_communication', 'dis_gifted'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $validated[$field] ?? false;
        }

        $validated['registration_no'] = PpdbRegistration::generateRegistrationNo();
        $validated['status'] = 'submitted';
        $validated['ip_address'] = $request->ip();
        $validated['academic_year_id'] = AcademicYear::active()?->id;

        $registration = PpdbRegistration::create($validated);

        session(['ppdb_registration_no' => $registration->registration_no]);

        return redirect()->route('ppdb.success')
            ->with('status', 'Pendaftaran berhasil! Nomor pendaftaran Anda: '.$registration->registration_no);
    }

    public function success(): View
    {
        $registration = null;
        if (session('ppdb_registration_no')) {
            $registration = PpdbRegistration::where('registration_no', session('ppdb_registration_no'))->first();
        }

        return view('pages.publik.ppdb.success', ['registration' => $registration]);
    }
}
