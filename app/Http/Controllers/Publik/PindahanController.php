<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Http\Requests\MutasiRegistrationRequest;
use App\Models\AcademicYear;
use App\Models\MutasiInterest;
use App\Models\MutasiRegistration;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PindahanController extends Controller
{
    public function index(): View
    {
        $registration = null;

        if (session('mutasi_registration_no')) {
            $registration = MutasiRegistration::where('registration_no', session('mutasi_registration_no'))->first();
        }

        if (! $this->isOpen()) {
            return view('pages.publik.pindahan.landing', [
                'registration' => $registration,
                'tahun' => AcademicYear::active(),
                'settings' => Setting::getAll(),
            ]);
        }

        return view('pages.publik.pindahan.index', [
            'registration' => $registration,
            'tahun' => AcademicYear::active(),
        ]);
    }

    public function store(MutasiRegistrationRequest $request): RedirectResponse
    {
        if (! $this->isOpen()) {
            return back()->withErrors(['mutasi' => 'Pendaftaran siswa pindahan sedang ditutup.']);
        }

        $validated = $request->validated();

        $validated['registration_no'] = MutasiRegistration::generateRegistrationNo();
        $validated['status'] = 'submitted';
        $validated['ip_address'] = $request->ip();
        $validated['academic_year_id'] = AcademicYear::active()?->id;

        $registration = MutasiRegistration::create($validated);

        session(['mutasi_registration_no' => $registration->registration_no]);

        return redirect()->route('pindahan.success')
            ->with('status', 'Pendaftaran pindahan berhasil! Nomor pendaftaran Anda: '.$registration->registration_no);
    }

    public function success(): View
    {
        $registration = null;
        if (session('mutasi_registration_no')) {
            $registration = MutasiRegistration::where('registration_no', session('mutasi_registration_no'))->first();
        }

        return view('pages.publik.pindahan.success', ['registration' => $registration]);
    }

    public function interestStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        MutasiInterest::updateOrCreate(
            ['phone' => $validated['phone']],
            ['name' => $validated['name']]
        );

        return back()->with('status', 'Terima kasih! Minat Anda tercatat. Kami akan menghubungi saat pendaftaran pindahan dibuka.');
    }

    protected function isOpen(): bool
    {
        return Setting::get('mutasi_status', 'closed') === 'open';
    }
}
