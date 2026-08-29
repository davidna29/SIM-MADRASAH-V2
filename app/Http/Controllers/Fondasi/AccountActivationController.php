<?php

namespace App\Http\Controllers\Fondasi;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\User;
use App\Support\AccountProvisioning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class AccountActivationController extends Controller
{
    /** Antrian siswa aktif tahun berjalan yang belum punya akun user. */
    public function index(): View
    {
        $tahun = AcademicYear::active();
        $yearId = $tahun?->id;

        $students = Student::query()
            ->with(['person', 'enrollments' => fn ($q) => $q->where('academic_year_id', $yearId)->where('status', 'aktif')->with('classGroup')])
            ->whereHas('enrollments', fn ($q) => $q->where('academic_year_id', $yearId)->where('status', 'aktif'))
            ->whereNotIn('id', User::whereNotNull('student_id')->pluck('student_id'))
            ->when(request('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('nis', 'like', "%{$s}%")
                    ->orWhere('nisn', 'like', "%{$s}%")
                    ->orWhereHas('person', fn ($p) => $p->where('name', 'like', "%{$s}%"));
            }))
            ->when(request('class_group_id'), fn ($q, $id) => $q->whereHas('enrollments', fn ($e) => $e->where('class_group_id', $id)->where('academic_year_id', $yearId)))
            ->orderBy('nis')
            ->get();

        $candidates = $students->map(function (Student $student) {
            $username = AccountProvisioning::usernameForStudent($student);

            return [
                'student' => $student,
                'complete' => $username !== null,
                'username' => $username ?? '—',
            ];
        });

        $complete = $candidates->where('complete', true)->values();
        $incomplete = $candidates->where('complete', false)->values();

        return view('pages.fondasi.pengguna.aktivasi', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Fondasi & Pengaturan', 'href' => route('dashboard')],
                ['label' => 'Pengguna & Role', 'href' => route('pengguna.index')],
                ['label' => 'Akun Menunggu Aktivasi'],
            ],
            'tahun' => $tahun,
            'years' => AcademicYear::orderByDesc('name')->pluck('name', 'id'),
            'classOptions' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->pluck('name', 'id'),
            'complete' => $complete,
            'incomplete' => $incomplete,
            'credentials' => session('aktivasi_credentials'),
            'failed' => session('aktivasi_failed'),
        ]);
    }

    /**
     * Bulk activation: User::updateOrCreate per siswa dalam satu transaksi.
     * Kegagalan per-baris dikumpulkan (tidak membatalkan batch).
     */
    public function activate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer'],
        ]);

        $tahun = AcademicYear::active();

        if (! $tahun) {
            return back()->withErrors(['student_ids' => 'Belum ada tahun ajaran aktif.']);
        }

        $studentIds = array_map('intval', $validated['student_ids']);
        $students = Student::with(['person', 'enrollments.classGroup'])
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        $ready = [];
        $failed = [];
        $credentials = [];

        foreach ($studentIds as $id) {
            $student = $students->get($id);

            if (! $student) {
                $failed[] = ['nama' => "Siswa #{$id}", 'alasan' => 'Data siswa tidak ditemukan.'];

                continue;
            }

            $enrollment = $student->enrollments
                ->where('academic_year_id', $tahun->id)
                ->where('status', 'aktif')
                ->first();

            if (! $enrollment) {
                $failed[] = ['nama' => $student->displayName(), 'alasan' => 'Tidak memiliki penempatan aktif tahun berjalan.'];

                continue;
            }

            $payload = AccountProvisioning::studentAccountPayload($student);

            if (! $payload['ok']) {
                $failed[] = ['nama' => $student->displayName(), 'alasan' => $payload['reason']];

                continue;
            }

            $ready[] = [$student, $payload['payload']];
            $credentials[] = [
                'nama' => $payload['payload']['name'],
                'username' => $payload['payload']['username'],
                'password' => $payload['payload']['password'],
                'kelas' => $enrollment->classGroup?->name ?? 'Tanpa rombel',
            ];
        }

        $created = 0;

        DB::transaction(function () use ($ready, &$created) {
            foreach ($ready as [$student, $payload]) {
                User::updateOrCreate(['student_id' => $student->id], $payload);

                activity('account_provisioning')
                    ->performedOn($student)
                    ->causedBy(auth()->user())
                    ->log('Akun siswa diaktifkan: '.$payload['username']);

                $created++;
            }
        });

        session(['aktivasi_credentials' => $credentials, 'aktivasi_failed' => $failed]);

        $status = "{$created} akun siswa berhasil dibuat.";

        if ($failed !== []) {
            $status .= ' '.count($failed).' gagal — periksa ringkasan.';
        }

        return redirect()->route('pengguna.aktivasi.index')->with('status', $status);
    }

    /**
     * Export CSV username+password batch terakhir — sekali unduh,
     * setelahnya data dibuang dari session (tidak disimpan plaintext).
     */
    public function exportCsv()
    {
        $rows = session('aktivasi_credentials');

        if (! $rows) {
            return back()->withErrors(['export' => 'Tidak ada daftar akun baru untuk diunduh.']);
        }

        $filename = 'akun-siswa-'.now()->format('Ymd-His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');

            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Nama', 'Username', 'Password Awal', 'Kelas / Rombel']);

            foreach ($rows as $row) {
                fputcsv($file, [$row['nama'], $row['username'], $row['password'], $row['kelas']]);
            }

            fclose($file);
        };

        session()->forget('aktivasi_credentials');

        return Response::stream($callback, 200, $headers);
    }
}
