<?php

namespace App\Http\Controllers\Ppdb;

use App\Exports\PpdbExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\PpdbRegistrationRequest;
use App\Models\PpdbRegistration;
use App\Support\PpdbService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdminPpdbController extends Controller
{
    public function index(): View
    {
        $query = PpdbRegistration::with('academicYear');

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('q')) {
            $search = request('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_no', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('pages.ppdb.index', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('dashboard')],
                ['label' => 'Pendaftar'],
            ],
            'registrations' => $registrations,
            'stats' => [
                'total' => PpdbRegistration::count(),
                'submitted' => PpdbRegistration::where('status', 'submitted')->count(),
                'accepted' => PpdbRegistration::where('status', 'accepted')->count(),
                'rejected' => PpdbRegistration::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(PpdbRegistration $registration): View
    {
        $registration->load('academicYear', 'student');

        return view('pages.ppdb.show', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('ppdb.index')],
                ['label' => $registration->registration_no],
            ],
            'registration' => $registration,
        ]);
    }

    public function edit(PpdbRegistration $registration): View
    {
        abort_unless($registration->status !== 'accepted', 403,
            'Pendaftar yang sudah diterima dikunci. Kelola data pendaftar di Master Data Siswa.');

        return view('pages.ppdb.edit', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('ppdb.index')],
                ['label' => $registration->registration_no],
                ['label' => 'Edit'],
            ],
            'registration' => $registration,
        ]);
    }

    public function update(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        abort_unless($registration->status !== 'accepted', 403,
            'Pendaftar yang sudah diterima dikunci. Kelola data pendaftar di Master Data Siswa.');

        $validated = $this->validateRegistration($request);

        $registration->update($validated);

        activity('ppdb')
            ->performedOn($registration)
            ->event('updated')
            ->log('Data PPDB diperbarui: '.$registration->name);

        return redirect()->route('ppdb.show', $registration)
            ->with('status', 'Data calon siswa berhasil diperbarui.');
    }

    public function accept(PpdbRegistration $registration): RedirectResponse
    {
        if ($registration->status !== 'submitted') {
            return back()->withErrors(['status' => 'Hanya pendaftar dengan status "submitted" yang bisa diterima.']);
        }

        PpdbService::accept($registration);

        return back()->with('status', $registration->name.' berhasil diterima. Lengkapi NIS & kelas di menu Data Siswa.');
    }

    public function reject(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        activity('ppdb')
            ->performedOn($registration)
            ->event('rejected')
            ->log('PPDB ditolak: '.$registration->name);

        return back()->with('status', $registration->name.' ditolak.');
    }

    /**
     * Validasi field pendaftaran (dipakai saat admin edit).
     * Boolean berkebutuhan khusus di-set false bila tidak dicentang.
     */
    protected function validateRegistration(Request $request): array
    {
        $validated = $request->validate((new PpdbRegistrationRequest)->rules());

        foreach (['dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual',
            'dis_behavioral', 'dis_slow_learner', 'dis_communication', 'dis_gifted'] as $field) {
            $validated[$field] = $validated[$field] ?? false;
        }

        return $validated;
    }

    public function exportExcel(Request $request)
    {
        $filename = 'ppdb-export-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            new PpdbExport($request->status, $request->q, $request->academic_year_id),
            $filename
        );
    }
}
