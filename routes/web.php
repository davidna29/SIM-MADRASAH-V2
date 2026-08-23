<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboardController;
use App\Support\DemoData;

Route::get('/', fn () => redirect()->route('login'));

// ============================================================
// Autentikasi
// ============================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    // Super Admin — dashboard & modul demo (Tahap 11)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('pages.dashboard', [
                'roleLabel' => 'Super Admin',
                'breadcrumb' => [['label' => 'Fondasi & Pengaturan'], ['label' => 'Dashboard']],
                'perluTindakan' => DemoData::perluTindakan(),
                'pengumuman' => DemoData::pengumuman(),
                'tagihan' => DemoData::tagihan(),
                'aktivitas' => DemoData::aktivitas(),
            ]);
        })->name('dashboard');

        Route::get('/akademik/data-siswa', function () {
            return view('pages.siswa.index', [
                'roleLabel' => 'Super Admin',
                'breadcrumb' => [
                    ['label' => 'Akademik', 'href' => route('dashboard')],
                    ['label' => 'Data Siswa'],
                ],
                'siswa' => DemoData::siswa(),
            ]);
        })->name('siswa.index');

        Route::get('/akademik/data-siswa/tambah', function () {
            return view('pages.siswa.create', [
                'roleLabel' => 'Super Admin',
                'breadcrumb' => [
                    ['label' => 'Akademik', 'href' => route('dashboard')],
                    ['label' => 'Data Siswa', 'href' => route('siswa.index')],
                    ['label' => 'Tambah Siswa'],
                ],
            ]);
        })->name('siswa.create');
    });

    // Guru — walking skeleton: penugasan → input nilai → terbitkan rapor
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/penugasan', [NilaiController::class, 'penugasan'])->name('penugasan');
        Route::get('/penugasan/{assignment}/nilai', [NilaiController::class, 'edit'])->name('nilai.edit');
        Route::post('/penugasan/{assignment}/nilai', [NilaiController::class, 'update'])->name('nilai.update');
        Route::post('/penugasan/{assignment}/terbitkan', [NilaiController::class, 'terbitkan'])->name('nilai.terbitkan');
        Route::get('/rapor/{report}', [NilaiController::class, 'rapor'])->name('rapor');
        Route::get('/rapor/{report}/unduh', [NilaiController::class, 'unduhRapor'])->name('rapor.unduh');
    });

    // Orang tua — walking skeleton: lihat rapor anak
    Route::middleware('role:orang_tua')->prefix('ortu')->name('ortu.')->group(function () {
        Route::get('/', [OrtuDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/rapor/{student}', [OrtuDashboardController::class, 'rapor'])->name('rapor');
        Route::get('/rapor/{student}/unduh', [OrtuDashboardController::class, 'unduh'])->name('rapor.unduh');
    });
});
