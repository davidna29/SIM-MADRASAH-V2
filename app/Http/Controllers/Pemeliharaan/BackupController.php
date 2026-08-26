<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use App\Support\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function __construct(private BackupService $backupService) {}

    public function index(): View
    {
        return view('pages.pemeliharaan.backup.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Backup & Restore'],
            ],
            'backups' => $this->backupService->listBackups(),
            'totalSize' => $this->backupService->getTotalSize(),
            'backupCount' => $this->backupService->getCount(),
        ]);
    }

    public function storeDb(): RedirectResponse
    {
        try {
            $filename = $this->backupService->createDatabaseBackup();

            activity('pemeliharaan')
                ->event('backup_created')
                ->withProperties(['type' => 'database', 'filename' => $filename])
                ->log('Backup database dibuat: '.$filename);

            return back()->with('status', "Backup database berhasil dibuat: {$filename}");
        } catch (\Throwable $e) {
            Log::error('Backup database gagal: '.$e->getMessage());

            return back()->withErrors(['backup' => 'Gagal membuat backup database: '.$e->getMessage()]);
        }
    }

    public function storeFiles(): RedirectResponse
    {
        try {
            $filename = $this->backupService->createFileBackup();

            activity('pemeliharaan')
                ->event('backup_created')
                ->withProperties(['type' => 'files', 'filename' => $filename])
                ->log('Backup file storage dibuat: '.$filename);

            return back()->with('status', "Backup file storage berhasil dibuat: {$filename}");
        } catch (\Throwable $e) {
            Log::error('Backup file storage gagal: '.$e->getMessage());

            return back()->withErrors(['backup' => 'Gagal membuat backup file: '.$e->getMessage()]);
        }
    }

    public function download(string $filename)
    {
        try {
            $path = $this->backupService->validateBackup($filename);

            activity('pemeliharaan')
                ->event('backup_downloaded')
                ->withProperties(['filename' => $filename])
                ->log('Backup diunduh: '.$filename);

            return response()->download($path, basename($filename));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['backup' => 'File backup tidak ditemukan.']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['backup' => $e->getMessage()]);
        }
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'backup_file' => 'required|file|max:102400|mimes:sql,zip',
        ]);

        try {
            $filename = $this->backupService->uploadBackup($request->file('backup_file'));

            activity('pemeliharaan')
                ->event('backup_uploaded')
                ->withProperties(['filename' => $filename])
                ->log('Backup diunggah: '.$filename);

            return back()->with('status', "File backup berhasil diunggah: {$filename}");
        } catch (\Throwable $e) {
            return back()->withErrors(['backup' => 'Gagal mengunggah file: '.$e->getMessage()]);
        }
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'filename' => 'required|string',
            'confirmation' => 'required|string',
        ]);

        if ($validated['confirmation'] !== 'RESTORE') {
            return back()->withErrors(['confirmation' => 'Ketik RESTORE untuk mengonfirmasi.']);
        }

        try {
            $this->backupService->restoreDatabase($validated['filename']);

            activity('pemeliharaan')
                ->event('backup_restored')
                ->withProperties(['filename' => $validated['filename']])
                ->log('Database direstore dari: '.$validated['filename']);

            return redirect()->route('backup.index')->with('status', 'Database berhasil direstore dari: '.$validated['filename']);
        } catch (\Throwable $e) {
            Log::error('Restore database gagal: '.$e->getMessage());

            return back()->withErrors(['restore' => 'Gagal me-restore database: '.$e->getMessage()]);
        }
    }

    public function destroy(string $filename): RedirectResponse
    {
        try {
            $this->backupService->deleteBackup($filename);

            activity('pemeliharaan')
                ->event('backup_deleted')
                ->withProperties(['filename' => $filename])
                ->log('Backup dihapus: '.$filename);

            return back()->with('status', "Backup '{$filename}' berhasil dihapus.");
        } catch (\RuntimeException $e) {
            return back()->withErrors(['backup' => 'File backup tidak ditemukan.']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['backup' => $e->getMessage()]);
        }
    }
}
