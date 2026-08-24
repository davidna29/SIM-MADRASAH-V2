<?php

use App\Http\Controllers\Akademik\AttendanceController;
use App\Http\Controllers\Akademik\ClassGroupController;
use App\Http\Controllers\Akademik\JurnalController;
use App\Http\Controllers\Akademik\ScheduleCellController;
use App\Http\Controllers\Akademik\ScheduleModelController;
use App\Http\Controllers\Akademik\StudentController;
use App\Http\Controllers\Akademik\SubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\JurnalController as GuruJurnalController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Kepegawaian\EmployeeController;
use App\Http\Controllers\Keuangan\TuitionController;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboardController;
use App\Http\Controllers\Ortu\SppController as OrtuSppController;
use App\Http\Controllers\Siswa\PortalController as SiswaPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// ============================================================
// Autentikasi
// ============================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard admin — ringkasan kondisi madrasah (bukan data demo)
    Route::middleware('role:super_admin|kepala_madrasah|wakamad_kurikulum|wakamad_kesiswaan|bendahara|tata_usaha')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    // Super Admin — modul demo (Tahap 11)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/akademik/data-siswa', [StudentController::class, 'index'])->name('siswa.index');
        Route::get('/akademik/data-siswa/tambah', [StudentController::class, 'create'])->name('siswa.create');
        Route::post('/akademik/data-siswa', [StudentController::class, 'store'])->name('siswa.store');
        Route::get('/akademik/data-siswa/{student}', [StudentController::class, 'show'])->name('siswa.show');
        Route::get('/akademik/data-siswa/{student}/edit', [StudentController::class, 'edit'])->name('siswa.edit');
        Route::put('/akademik/data-siswa/{student}', [StudentController::class, 'update'])->name('siswa.update');
        Route::delete('/akademik/data-siswa/{student}', [StudentController::class, 'destroy'])->name('siswa.destroy');

        // Modul Data Guru & Pegawai — backend (Tahap 13)
        Route::get('/kepegawaian/data-guru', [EmployeeController::class, 'index'])->name('pegawai.index');
        Route::get('/kepegawaian/data-guru/tambah', [EmployeeController::class, 'create'])->name('pegawai.create');
        Route::post('/kepegawaian/data-guru', [EmployeeController::class, 'store'])->name('pegawai.store');
        Route::get('/kepegawaian/data-guru/{employee}', [EmployeeController::class, 'show'])->name('pegawai.show');
        Route::get('/kepegawaian/data-guru/{employee}/edit', [EmployeeController::class, 'edit'])->name('pegawai.edit');
        Route::put('/kepegawaian/data-guru/{employee}', [EmployeeController::class, 'update'])->name('pegawai.update');
        Route::delete('/kepegawaian/data-guru/{employee}', [EmployeeController::class, 'destroy'])->name('pegawai.destroy');

        // Modul Mata Pelajaran
        Route::get('/akademik/mata-pelajaran', [SubjectController::class, 'index'])->name('mapel.index');
        Route::post('/akademik/mata-pelajaran', [SubjectController::class, 'store'])->name('mapel.store');
        Route::post('/akademik/mata-pelajaran/urutan', [SubjectController::class, 'reorder'])->name('mapel.reorder');
        Route::put('/akademik/mata-pelajaran/{subject}', [SubjectController::class, 'update'])->name('mapel.update');
        Route::delete('/akademik/mata-pelajaran/{subject}', [SubjectController::class, 'destroy'])->name('mapel.destroy');

        // Modul Kelas & Penempatan
        Route::get('/akademik/kelas', [ClassGroupController::class, 'index'])->name('kelas.index');
        Route::get('/akademik/kelas/tambah', [ClassGroupController::class, 'create'])->name('kelas.create');
        Route::post('/akademik/kelas', [ClassGroupController::class, 'store'])->name('kelas.store');
        Route::get('/akademik/kelas/{classGroup}', [ClassGroupController::class, 'show'])->name('kelas.show');
        Route::get('/akademik/kelas/{classGroup}/edit', [ClassGroupController::class, 'edit'])->name('kelas.edit');
        Route::put('/akademik/kelas/{classGroup}', [ClassGroupController::class, 'update'])->name('kelas.update');
        Route::delete('/akademik/kelas/{classGroup}', [ClassGroupController::class, 'destroy'])->name('kelas.destroy');
        Route::post('/akademik/kelas/{classGroup}/penempatan', [ClassGroupController::class, 'place'])->name('kelas.place');
        Route::post('/akademik/kelas/{classGroup}/penempatan/{enrollment}/lepas', [ClassGroupController::class, 'unplace'])->name('kelas.unplace');

        // Modul Jadwal Pelajaran — Model Jadwal
        Route::get('/akademik/jadwal-pelajaran', fn () => redirect()->route('jadwal.model.index'))->name('jadwal.index');
        Route::get('/akademik/jadwal-pelajaran/model', [ScheduleModelController::class, 'index'])->name('jadwal.model.index');
        Route::get('/akademik/jadwal-pelajaran/model/tambah', [ScheduleModelController::class, 'create'])->name('jadwal.model.create');
        Route::post('/akademik/jadwal-pelajaran/model', [ScheduleModelController::class, 'store'])->name('jadwal.model.store');
        Route::get('/akademik/jadwal-pelajaran/model/{model}', [ScheduleModelController::class, 'show'])->name('jadwal.model.show');
        Route::get('/akademik/jadwal-pelajaran/model/{model}/edit', [ScheduleModelController::class, 'edit'])->name('jadwal.model.edit');
        Route::put('/akademik/jadwal-pelajaran/model/{model}', [ScheduleModelController::class, 'update'])->name('jadwal.model.update');
        Route::delete('/akademik/jadwal-pelajaran/model/{model}', [ScheduleModelController::class, 'destroy'])->name('jadwal.model.destroy');

        // Penyusunan (tabel master) + view turunan
        Route::get('/akademik/jadwal-pelajaran/penyusunan', [ScheduleCellController::class, 'penyusunan'])->name('jadwal.penyusunan');
        Route::post('/akademik/jadwal-pelajaran/penyusunan/{model}', [ScheduleCellController::class, 'store'])->name('jadwal.penyusunan.store');
        Route::post('/akademik/jadwal-pelajaran/penyusunan/{model}/generate', [ScheduleCellController::class, 'generate'])->name('jadwal.generate');
        Route::get('/akademik/jadwal-pelajaran/kelas/{classGroup}', [ScheduleCellController::class, 'perKelas'])->name('jadwal.kelas');
        Route::get('/akademik/jadwal-pelajaran/kelas/{classGroup}/cetak', [ScheduleCellController::class, 'cetakKelas'])->name('jadwal.kelas.cetak');
        Route::get('/akademik/jadwal-pelajaran/guru/{teacher}', [ScheduleCellController::class, 'perGuru'])->name('jadwal.guru');
        Route::get('/akademik/jadwal-pelajaran/guru/{teacher}/cetak', [ScheduleCellController::class, 'cetakGuru'])->name('jadwal.guru.cetak');
    });

    // Jurnal Mengajar — monitor (Wakamad Kurikulum / Kepala Madrasah)
    Route::middleware('role:super_admin|wakamad_kurikulum|kepala_madrasah')->group(function () {
        Route::get('/akademik/jurnal-mengajar', [JurnalController::class, 'index'])->name('jurnal.admin.index');
    });

    // Kehadiran Siswa — input harian + rekap bulanan (input tanggal lampau hanya untuk role privileged)
    Route::middleware('role:super_admin|wakamad_kesiswaan|wali_kelas|guru|kepala_madrasah|wakamad_kurikulum')->group(function () {
        Route::get('/kesiswaan/kehadiran', [AttendanceController::class, 'index'])->name('kehadiran.index');
        Route::post('/kesiswaan/kehadiran', [AttendanceController::class, 'store'])->name('kehadiran.store');
        Route::get('/kesiswaan/kehadiran/rekap-bulanan', [AttendanceController::class, 'rekapBulanan'])->name('kehadiran.rekap');
    });

    // Jurnal Mingguan per Kelas & per Guru — tampilan agregat (semua guru, TU, wakamad, kepala, super admin)
    Route::middleware('role:guru|tata_usaha|wakamad_kurikulum|kepala_madrasah|super_admin')->group(function () {
        Route::get('/akademik/jurnal-mengajar/mingguan', [JurnalController::class, 'mingguan'])->name('jurnal.admin.mingguan');
        Route::get('/akademik/jurnal-mengajar/mingguan-guru', [JurnalController::class, 'mingguanGuru'])->name('jurnal.admin.mingguan.guru');
    });

    // Guru — walking skeleton: penugasan → input nilai → terbitkan rapor
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/penugasan', [NilaiController::class, 'penugasan'])->name('penugasan');
        Route::get('/penugasan/{assignment}/nilai', [NilaiController::class, 'edit'])->name('nilai.edit');
        Route::post('/penugasan/{assignment}/nilai', [NilaiController::class, 'update'])->name('nilai.update');
        Route::post('/penugasan/{assignment}/terbitkan', [NilaiController::class, 'terbitkan'])->name('nilai.terbitkan');
        Route::get('/rapor/{report}', [NilaiController::class, 'rapor'])->name('rapor');
        Route::get('/rapor/{report}/unduh', [NilaiController::class, 'unduhRapor'])->name('rapor.unduh');

        // Jurnal Mengajar — guru mencatat jurnal per penugasan
        Route::get('/jurnal', [GuruJurnalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/{assignment}', [GuruJurnalController::class, 'show'])->name('jurnal.show');
        Route::post('/jurnal/{assignment}', [GuruJurnalController::class, 'store'])->name('jurnal.store');
        Route::get('/jurnal/{assignment}/entri/{journal}/edit', [GuruJurnalController::class, 'edit'])->name('jurnal.edit');
        Route::put('/jurnal/{assignment}/entri/{journal}', [GuruJurnalController::class, 'update'])->name('jurnal.update');
        Route::delete('/jurnal/entri/{journal}', [GuruJurnalController::class, 'destroy'])->name('jurnal.destroy');
        Route::get('/jurnal/{assignment}/entri/{journal}/lampiran', [GuruJurnalController::class, 'lampiran'])->name('jurnal.lampiran');
    });

    // Orang tua — walking skeleton: lihat rapor anak
    Route::middleware('role:orang_tua')->prefix('ortu')->name('ortu.')->group(function () {
        Route::get('/', [OrtuDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/rapor/{student}', [OrtuDashboardController::class, 'rapor'])->name('rapor');
        Route::get('/rapor/{student}/unduh', [OrtuDashboardController::class, 'unduh'])->name('rapor.unduh');
        Route::get('/spp', [OrtuSppController::class, 'index'])->name('spp.index');
        Route::get('/spp/{student}', [OrtuSppController::class, 'show'])->name('spp.show');
        Route::get('/anak/{student}', [OrtuDashboardController::class, 'ringkasan'])->name('ringkasan');
    });

    // Portal Siswa — data diri sendiri (read-only)
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [SiswaPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/rapor', [SiswaPortalController::class, 'rapor'])->name('rapor');
        Route::get('/rapor/unduh', [SiswaPortalController::class, 'raporUnduh'])->name('rapor.unduh');
        Route::get('/spp', [SiswaPortalController::class, 'spp'])->name('spp');
    });

    // SPP — rekap & pembayaran. Index boleh dilihat kepala_madrasah (read-only);
    // kelola (bayar/nominal/keringanan) hanya bendahara/tata_usaha/super_admin.
    Route::middleware('role:super_admin|bendahara|tata_usaha|kepala_madrasah')->group(function () {
        Route::get('/keuangan/spp', [TuitionController::class, 'index'])->name('spp.index');
    });

    Route::middleware('role:super_admin|bendahara|tata_usaha')->group(function () {
        Route::get('/keuangan/spp/nominal', [TuitionController::class, 'settings'])->name('spp.settings');
        Route::post('/keuangan/spp/nominal', [TuitionController::class, 'settingsStore'])->name('spp.settings.store');
        Route::get('/keuangan/spp/keringanan', [TuitionController::class, 'overrides'])->name('spp.overrides');
        Route::post('/keuangan/spp/keringanan', [TuitionController::class, 'overridesStore'])->name('spp.overrides.store');
        Route::post('/keuangan/spp/bayar', [TuitionController::class, 'pay'])->name('spp.pay');
    });
});
