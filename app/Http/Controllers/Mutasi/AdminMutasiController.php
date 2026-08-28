<?php

namespace App\Http\Controllers\Mutasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\MutasiRegistrationRequest;
use App\Models\MutasiRegistration;
use App\Support\MutasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMutasiController extends Controller
{
    public function index(): View
    {
        $query = MutasiRegistration::query()
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->when(request('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('registration_no', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%");
            }));

        $registrations = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('pages.mutasi.index', [
            'roleLabel' => 'Mutasi',
            'breadcrumb' => [
                ['label' => 'Mutasi Masuk', 'href' => route('dashboard')],
                ['label' => 'Pendaftar Pindah'],
            ],
            'registrations' => $registrations,
            'stats' => [
                'total' => MutasiRegistration::count(),
                'submitted' => MutasiRegistration::where('status', 'submitted')->count(),
                'accepted' => MutasiRegistration::where('status', 'accepted')->count(),
                'rejected' => MutasiRegistration::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(MutasiRegistration $registration): View
    {
        $registration->load('academicYear', 'student');

        return view('pages.mutasi.show', [
            'roleLabel' => 'Mutasi',
            'breadcrumb' => [
                ['label' => 'Mutasi Masuk', 'href' => route('mutasi.index')],
                ['label' => $registration->registration_no],
            ],
            'registration' => $registration,
        ]);
    }

    public function edit(MutasiRegistration $registration): View
    {
        abort_unless($registration->status !== 'accepted', 403,
            'Pendaftar pindah yang sudah diterima dikunci. Kelola data di Master Data Siswa.');

        return view('pages.mutasi.edit', [
            'roleLabel' => 'Mutasi',
            'breadcrumb' => [
                ['label' => 'Mutasi Masuk', 'href' => route('mutasi.index')],
                ['label' => $registration->registration_no],
                ['label' => 'Edit'],
            ],
            'registration' => $registration,
        ]);
    }

    public function update(MutasiRegistrationRequest $request, MutasiRegistration $registration): RedirectResponse
    {
        abort_unless($registration->status !== 'accepted', 403,
            'Pendaftar pindah yang sudah diterima dikunci. Kelola data di Master Data Siswa.');

        $validated = $request->validated();

        $registration->update($validated);

        activity('mutasi')
            ->performedOn($registration)
            ->event('updated')
            ->log('Data pindahan diperbarui: '.$registration->name);

        return redirect()->route('mutasi.show', $registration)
            ->with('status', 'Data pendaftar pindah berhasil diperbarui.');
    }

    public function accept(MutasiRegistration $registration): RedirectResponse
    {
        if ($registration->status !== 'submitted') {
            return back()->withErrors(['status' => 'Hanya pendaftar dengan status "submitted" yang bisa diterima.']);
        }

        MutasiService::accept($registration);

        return back()->with('status', $registration->name.' berhasil diterima. Lengkapi NIS & kelas di menu Data Siswa.');
    }

    public function reject(Request $request, MutasiRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        activity('mutasi')
            ->performedOn($registration)
            ->event('rejected')
            ->log('Mutasi ditolak: '.$registration->name);

        return back()->with('status', 'Pendaftar pindah ditolak.');
    }
}
