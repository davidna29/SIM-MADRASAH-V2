<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Http\Requests\PpdbRegistrationRequest;
use App\Models\AcademicYear;
use App\Models\PpdbInterest;
use App\Models\PpdbRegistration;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        // Saat PPDB ditutup, /ppdb menjadi landing page informasi (bukan form).
        if (! $this->isOpen()) {
            return view('pages.publik.ppdb.landing', [
                'registration' => $registration,
                'tahun' => AcademicYear::active(),
                'settings' => Setting::getAll(),
            ]);
        }

        return view('pages.publik.ppdb.index', [
            'registration' => $registration,
            'tahun' => AcademicYear::active(),
        ]);
    }

    public function store(PpdbRegistrationRequest $request)
    {
        // Guard: form hanya dapat di-submit ketika PPDB dibuka.
        if (! $this->isOpen()) {
            return back()->withErrors(['ppdb' => 'Pendaftaran PPDB sedang ditutup.']);
        }

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

    public function interestStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        // Dedupe per nomor telepon (updateOrCreate) agar tidak spam; simpan nama terbaru.
        PpdbInterest::updateOrCreate(
            ['phone' => $validated['phone']],
            ['name' => $validated['name']]
        );

        return back()->with('status', 'Terima kasih! Minat Anda tercatat. Kami akan menghubungi saat pendaftaran dibuka.');
    }

    /**
     * Apakah pendaftaran PPDB sedang dibuka (saklar admin)?
     */
    protected function isOpen(): bool
    {
        return Setting::get('ppdb_status', 'closed') === 'open';
    }
}
