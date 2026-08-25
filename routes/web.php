<?php

use App\Http\Controllers\Akademik\AttendanceController;
use App\Http\Controllers\Akademik\ClassGroupController;
use App\Http\Controllers\Akademik\JurnalController;
use App\Http\Controllers\Akademik\ScheduleCellController;
use App\Http\Controllers\Akademik\ScheduleModelController;
use App\Http\Controllers\Akademik\StudentController;
use App\Http\Controllers\Akademik\SubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cms\AgendaController;
use App\Http\Controllers\Cms\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\JurnalController as GuruJurnalController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Kepegawaian\EmployeeController;
use App\Http\Controllers\Kesiswaan\AchievementController;
use App\Http\Controllers\Kesiswaan\OffenseController;
use App\Http\Controllers\Keuangan\TuitionController;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboardController;
use App\Http\Controllers\Ortu\SppController as OrtuSppController;
use App\Http\Controllers\Publik\AgendaController as PublikAgendaController;
use App\Http\Controllers\Publik\BeritaController as PublikBeritaController;
use App\Http\Controllers\Siswa\PortalController as SiswaPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Website publik (tanpa autentikasi)
Route::get('/berita', [PublikBeritaController::class, 'index'])->name('publik.berita.index');
Route::get('/berita/{article:slug}', [PublikBeritaController::class, 'show'])->name('publik.berita.show');
Route::get('/agenda', [PublikAgendaController::class, 'index'])->name('publik.agenda.index');

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

    // CMS — Berita & Agenda (kontributor guru + editor/humas/kepala/TU/super admin)
    Route::middleware('role:super_admin|wakamad_humas|editor_berita|kepala_madrasah|tata_usaha|guru')->prefix('publikasi')->name('cms.')->group(function () {
        Route::get('/berita', [ArticleController::class, 'index'])->name('berita.index');
        Route::get('/berita/tambah', [ArticleController::class, 'create'])->name('berita.create');
        Route::post('/berita', [ArticleController::class, 'store'])->name('berita.store');
        Route::get('/berita/{article}', [ArticleController::class, 'show'])->name('berita.show');
        Route::get('/berita/{article}/edit', [ArticleController::class, 'edit'])->name('berita.edit');
        Route::put('/berita/{article}', [ArticleController::class, 'update'])->name('berita.update');
        Route::delete('/berita/{article}', [ArticleController::class, 'destroy'])->name('berita.destroy');
        Route::post('/berita/{article}/transisi/{aksi}', [ArticleController::class, 'transition'])->name('berita.transition');

        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/tambah', [AgendaController::class, 'create'])->name('agenda.create');
        Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
        Route::get('/agenda/{agenda}/edit', [AgendaController::class, 'edit'])->name('agenda.edit');
        Route::put('/agenda/{agenda}', [AgendaController::class, 'update'])->name('agenda.update');
        Route::delete('/agenda/{agenda}', [AgendaController::class, 'destroy'])->name('agenda.destroy');
    });

    // Kesiswaan — Prestasi & Pelanggaran
    Route::middleware('role:super_admin|wakamad_kesiswaan|wali_kelas|guru|guru_bk|kepala_madrasah')->group(function () {
        Route::get('/kesiswaan/prestasi', [AchievementController::class, 'index'])->name('prestasi.index');
        Route::get('/kesiswaan/prestasi/template', [AchievementController::class, 'template'])->name('prestasi.template');
        Route::get('/kesiswaan/prestasi/import', [AchievementController::class, 'import'])->name('prestasi.import');
        Route::post('/kesiswaan/prestasi/import/preview', [AchievementController::class, 'processImport'])->name('prestasi.import.process');
        Route::get('/kesiswaan/prestasi/import/preview', [AchievementController::class, 'previewImport'])->name('prestasi.import.preview');
        Route::post('/kesiswaan/prestasi/import/simpan', [AchievementController::class, 'simpanImport'])->name('prestasi.import.simpan');
        Route::post('/kesiswaan/prestasi/import/batal', [AchievementController::class, 'batalImport'])->name('prestasi.import.batal');
        Route::get('/kesiswaan/prestasi/tambah', [AchievementController::class, 'create'])->name('prestasi.create');
        Route::post('/kesiswaan/prestasi', [AchievementController::class, 'store'])->name('prestasi.store');
        Route::get('/kesiswaan/prestasi/{achievement}/edit', [AchievementController::class, 'edit'])->name('prestasi.edit');
        Route::put('/kesiswaan/prestasi/{achievement}', [AchievementController::class, 'update'])->name('prestasi.update');
        Route::delete('/kesiswaan/prestasi/{achievement}', [AchievementController::class, 'destroy'])->name('prestasi.destroy');
        Route::post('/kesiswaan/prestasi/{achievement}/verifikasi', [AchievementController::class, 'verifikasi'])->name('prestasi.verifikasi');
        Route::post('/kesiswaan/prestasi/{achievement}/publikasi', [AchievementController::class, 'publikasi'])->name('prestasi.publikasi');

        Route::get('/kesiswaan/pelanggaran', [OffenseController::class, 'index'])->name('pelanggaran.index');
        Route::get('/kesiswaan/pelanggaran/tambah', [OffenseController::class, 'create'])->name('pelanggaran.create');
        Route::post('/kesiswaan/pelanggaran', [OffenseController::class, 'store'])->name('pelanggaran.store');
        Route::get('/kesiswaan/pelanggaran/{offense}/edit', [OffenseController::class, 'edit'])->name('pelanggaran.edit');
        Route::put('/kesiswaan/pelanggaran/{offense}', [OffenseController::class, 'update'])->name('pelanggaran.update');
        Route::delete('/kesiswaan/pelanggaran/{offense}', [OffenseController::class, 'destroy'])->name('pelanggaran.destroy');
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
