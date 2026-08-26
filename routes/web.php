<?php

use App\Http\Controllers\Akademik\AttendanceController;
use App\Http\Controllers\Akademik\ClassGroupController;
use App\Http\Controllers\Akademik\HomeroomController;
use App\Http\Controllers\Akademik\JurnalController;
use App\Http\Controllers\Akademik\ScheduleCellController;
use App\Http\Controllers\Akademik\ScheduleModelController;
use App\Http\Controllers\Akademik\StudentController;
use App\Http\Controllers\Akademik\SubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cms\AgendaController;
use App\Http\Controllers\Cms\ArticleController;
use App\Http\Controllers\Cms\GalleryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Fondasi\UserController;
use App\Http\Controllers\Guru\JurnalController as GuruJurnalController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Kepegawaian\EmployeeController;
use App\Http\Controllers\Kesiswaan\AchievementController;
use App\Http\Controllers\Kesiswaan\CounselingController;
use App\Http\Controllers\Kesiswaan\ExtracurricularController;
use App\Http\Controllers\Kesiswaan\OffenseController;
use App\Http\Controllers\Kesiswaan\PortofolioController;
use App\Http\Controllers\Keuangan\TuitionController;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboardController;
use App\Http\Controllers\Ortu\SppController as OrtuSppController;
use App\Http\Controllers\Pemeliharaan\ActivityLogController;
use App\Http\Controllers\Pemeliharaan\LaporanController;
use App\Http\Controllers\Perpustakaan\LibraryController;
use App\Http\Controllers\Publik\AgendaController as PublikAgendaController;
use App\Http\Controllers\Publik\BeritaController as PublikBeritaController;
use App\Http\Controllers\Publik\GaleriController as PublikGaleriController;
use App\Http\Controllers\Sarpras\InventoryController;
use App\Http\Controllers\Siswa\PortalController as SiswaPortalController;
use App\Http\Controllers\Tu\LetterController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Website publik (tanpa autentikasi)
Route::get('/berita', [PublikBeritaController::class, 'index'])->name('publik.berita.index');
Route::get('/berita/{article:slug}', [PublikBeritaController::class, 'show'])->name('publik.berita.show');
Route::get('/agenda', [PublikAgendaController::class, 'index'])->name('publik.agenda.index');
Route::get('/galeri', [PublikGaleriController::class, 'index'])->name('publik.galeri.index');
Route::get('/galeri/{album:slug}', [PublikGaleriController::class, 'show'])->name('publik.galeri.show');

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

        // Activity & Audit Log — pemeliharaan
        Route::get('/pemeliharaan/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

        // Pengguna & Role Management
        Route::get('/fondasi/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::get('/fondasi/pengguna/tambah', [UserController::class, 'create'])->name('pengguna.create');
        Route::post('/fondasi/pengguna', [UserController::class, 'store'])->name('pengguna.store');
        Route::get('/fondasi/pengguna/{user}', [UserController::class, 'show'])->name('pengguna.show');
        Route::get('/fondasi/pengguna/{user}/edit', [UserController::class, 'edit'])->name('pengguna.edit');
        Route::put('/fondasi/pengguna/{user}', [UserController::class, 'update'])->name('pengguna.update');
        Route::delete('/fondasi/pengguna/{user}', [UserController::class, 'destroy'])->name('pengguna.destroy');
    });

    // Pusat Laporan — multi-role
    Route::middleware('role:super_admin|kepala_madrasah|wakamad_kurikulum|wakamad_kesiswaan|bendahara')
        ->prefix('pemeliharaan/laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/akademik', [LaporanController::class, 'akademik'])->name('akademik');
            Route::get('/kehadiran', [LaporanController::class, 'kehadiran'])->name('kehadiran');
            Route::get('/keuangan', [LaporanController::class, 'keuangan'])->name('keuangan');
            Route::get('/kesiswaan', [LaporanController::class, 'kesiswaan'])->name('kesiswaan');
            Route::get('/tenaga', [LaporanController::class, 'tenaga'])->name('tenaga');
            Route::get('/perpustakaan', [LaporanController::class, 'perpustakaan'])->name('perpustakaan');
            Route::get('/{jenis}/pdf', [LaporanController::class, 'exportPdf'])->name('pdf');
            Route::get('/{jenis}/csv', [LaporanController::class, 'exportCsv'])->name('csv');
        });

    // Jurnal Mengajar — monitor (Wakamad Kurikulum / Kepala Madrasah)
    Route::middleware('role:super_admin|wakamad_kurikulum|kepala_madrasah')->group(function () {
        Route::get('/akademik/jurnal-mengajar', [JurnalController::class, 'index'])->name('jurnal.admin.index');
    });

    // Wali Kelas (Homeroom) — accessible by super_admin + wakamad_kurikulum
    Route::middleware('role:super_admin|wakamad_kurikulum')->group(function () {
        Route::post('/akademik/kelas/{classGroup}/wali-kelas', [HomeroomController::class, 'store'])->name('kelas.wali.store');
        Route::delete('/akademik/kelas/{classGroup}/wali-kelas/{homeroom}', [HomeroomController::class, 'destroy'])->name('kelas.wali.destroy');
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

        // Galeri & Media
        Route::get('/galeri', [GalleryController::class, 'index'])->name('galeri.index');
        Route::get('/galeri/tambah', [GalleryController::class, 'create'])->name('galeri.create');
        Route::post('/galeri', [GalleryController::class, 'store'])->name('galeri.store');
        Route::get('/galeri/{album}', [GalleryController::class, 'show'])->name('galeri.show');
        Route::get('/galeri/{album}/edit', [GalleryController::class, 'edit'])->name('galeri.edit');
        Route::put('/galeri/{album}', [GalleryController::class, 'update'])->name('galeri.update');
        Route::delete('/galeri/{album}', [GalleryController::class, 'destroy'])->name('galeri.destroy');
        Route::post('/galeri/{album}/foto', [GalleryController::class, 'uploadPhotos'])->name('galeri.foto');
        Route::post('/galeri/{album}/video', [GalleryController::class, 'addVideo'])->name('galeri.video');
        Route::post('/galeri/{album}/cover/{item}', [GalleryController::class, 'setCover'])->name('galeri.cover');
        Route::delete('/galeri/{album}/item/{item}', [GalleryController::class, 'destroyItem'])->name('galeri.item.destroy');
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

        // Ekstrakurikuler
        // Ekstrakurikuler
        Route::get('/kesiswaan/ekstrakurikuler', [ExtracurricularController::class, 'index'])->name('ekskul.index');
        Route::get('/kesiswaan/ekstrakurikuler/tambah', [ExtracurricularController::class, 'create'])->name('ekskul.create');
        Route::post('/kesiswaan/ekstrakurikuler', [ExtracurricularController::class, 'store'])->name('ekskul.store');
        Route::get('/kesiswaan/ekstrakurikuler/{ekskul}', [ExtracurricularController::class, 'show'])->name('ekskul.show');
        Route::get('/kesiswaan/ekstrakurikuler/{ekskul}/edit', [ExtracurricularController::class, 'edit'])->name('ekskul.edit');
        Route::put('/kesiswaan/ekstrakurikuler/{ekskul}', [ExtracurricularController::class, 'update'])->name('ekskul.update');
        Route::delete('/kesiswaan/ekstrakurikuler/{ekskul}', [ExtracurricularController::class, 'destroy'])->name('ekskul.destroy');
        Route::post('/kesiswaan/ekstrakurikuler/{ekskul}/anggota', [ExtracurricularController::class, 'memberStore'])->name('ekskul.member.store');
        Route::delete('/kesiswaan/ekstrakurikuler/{ekskul}/anggota/{member}', [ExtracurricularController::class, 'memberDestroy'])->name('ekskul.member.destroy');
        Route::post('/kesiswaan/ekstrakurikuler/{ekskul}/presensi', [ExtracurricularController::class, 'presensi'])->name('ekskul.presensi');

        // Konseling (BK)
        Route::get('/kesiswaan/konseling', [CounselingController::class, 'index'])->name('konseling.index');
        Route::get('/kesiswaan/konseling/tambah', [CounselingController::class, 'create'])->name('konseling.create');
        Route::post('/kesiswaan/konseling', [CounselingController::class, 'store'])->name('konseling.store');
        Route::get('/kesiswaan/konseling/{session}', [CounselingController::class, 'show'])->name('konseling.show');
        Route::get('/kesiswaan/konseling/{session}/edit', [CounselingController::class, 'edit'])->name('konseling.edit');
        Route::put('/kesiswaan/konseling/{session}', [CounselingController::class, 'update'])->name('konseling.update');
        Route::delete('/kesiswaan/konseling/{session}', [CounselingController::class, 'destroy'])->name('konseling.destroy');
    });

    // Portofolio Digital — agregasi read-only data siswa
    Route::middleware('role:super_admin|wakamad_kesiswaan|wali_kelas|guru_bk|kepala_madrasah')
        ->prefix('kesiswaan/portofolio')->name('portofolio.')->group(function () {
            Route::get('/', [PortofolioController::class, 'index'])->name('index');
            Route::get('/{student}', [PortofolioController::class, 'show'])->name('show');
            Route::get('/{student}/qr', [PortofolioController::class, 'qr'])->name('qr');
            Route::get('/{student}/cetak', [PortofolioController::class, 'print'])->name('print');
        });

    // Verifikasi portofolio publik (harus login)
    Route::get('/portofolio/{token}', [PortofolioController::class, 'verify'])->name('portofolio.verify')
        ->middleware('role:super_admin|wakamad_kesiswaan|wali_kelas|guru_bk|kepala_madrasah|guru|bendahara|tata_usaha|orang_tua|siswa');

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

    // Perpustakaan — kelola oleh super_admin/pustakawan; kepala_madrasah read-only
    Route::middleware('role:super_admin|pustakawan|kepala_madrasah')->prefix('perpustakaan')->name('perpustakaan.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::get('/tambah', [LibraryController::class, 'create'])->name('create');
        Route::post('/', [LibraryController::class, 'store'])->name('store');

        // Kategori — sebelum rute {book} agar tidak tertangkap wildcard
        Route::get('/kategori', [LibraryController::class, 'categoryIndex'])->name('kategori.index');
        Route::post('/kategori', [LibraryController::class, 'categoryStore'])->name('kategori.store');
        Route::put('/kategori/{category}', [LibraryController::class, 'categoryUpdate'])->name('kategori.update');
        Route::delete('/kategori/{category}', [LibraryController::class, 'categoryDestroy'])->name('kategori.destroy');

        // Anggota — sebelum rute {book}
        Route::get('/anggota', [LibraryController::class, 'memberIndex'])->name('anggota.index');
        Route::post('/anggota', [LibraryController::class, 'memberStore'])->name('anggota.store');
        Route::delete('/anggota/{member}', [LibraryController::class, 'memberDestroy'])->name('anggota.destroy');

        Route::get('/{book}', [LibraryController::class, 'show'])->name('show');
        Route::get('/{book}/edit', [LibraryController::class, 'edit'])->name('edit');
        Route::put('/{book}', [LibraryController::class, 'update'])->name('update');
        Route::delete('/{book}', [LibraryController::class, 'destroy'])->name('destroy');

        // Pinjam / Kembalikan
        Route::post('/{book}/pinjam', [LibraryController::class, 'loanStore'])->name('loan.store');
        Route::post('/{book}/kembali/{loan}', [LibraryController::class, 'loanReturn'])->name('loan.return');
    });

    // Inventaris Barang (Sarpras) — kepala_madrasah read-only; kelola oleh super_admin/wakamad_sarpras/tata_usaha
    Route::middleware('role:super_admin|wakamad_sarpras|tata_usaha|kepala_madrasah')->group(function () {
        Route::get('/sarpras/inventaris', [InventoryController::class, 'index'])->name('inventaris.index');
        Route::get('/sarpras/inventaris/tambah', [InventoryController::class, 'create'])->name('inventaris.create');
        Route::post('/sarpras/inventaris', [InventoryController::class, 'store'])->name('inventaris.store');

        // Kategori barang — sebelum rute {item} agar tidak tertangkap wildcard
        Route::get('/sarpras/inventaris/kategori', [InventoryController::class, 'categoryIndex'])->name('inventaris.kategori.index');
        Route::post('/sarpras/inventaris/kategori', [InventoryController::class, 'categoryStore'])->name('inventaris.kategori.store');
        Route::put('/sarpras/inventaris/kategori/{category}', [InventoryController::class, 'categoryUpdate'])->name('inventaris.kategori.update');
        Route::delete('/sarpras/inventaris/kategori/{category}', [InventoryController::class, 'categoryDestroy'])->name('inventaris.kategori.destroy');

        Route::get('/sarpras/inventaris/{item}', [InventoryController::class, 'show'])->name('inventaris.show');
        Route::get('/sarpras/inventaris/{item}/edit', [InventoryController::class, 'edit'])->name('inventaris.edit');
        Route::put('/sarpras/inventaris/{item}', [InventoryController::class, 'update'])->name('inventaris.update');
        Route::delete('/sarpras/inventaris/{item}', [InventoryController::class, 'destroy'])->name('inventaris.destroy');

        // Mutasi barang
        Route::post('/sarpras/inventaris/{item}/mutasi', [InventoryController::class, 'mutationStore'])->name('inventaris.mutasi.store');
        Route::post('/sarpras/inventaris/{item}/mutasi/{mutation}/setujui', [InventoryController::class, 'mutationApprove'])->name('inventaris.mutasi.approve');
        Route::post('/sarpras/inventaris/{item}/mutasi/{mutation}/tolak', [InventoryController::class, 'mutationReject'])->name('inventaris.mutasi.reject');
        Route::delete('/sarpras/inventaris/{item}/mutasi/{mutation}', [InventoryController::class, 'mutationDestroy'])->name('inventaris.mutasi.destroy');

        // Pemeliharaan barang
        Route::post('/sarpras/inventaris/{item}/pemeliharaan', [InventoryController::class, 'maintenanceStore'])->name('inventaris.perawatan.store');
        Route::post('/sarpras/inventaris/{item}/pemeliharaan/{maintenance}/selesai', [InventoryController::class, 'maintenanceDone'])->name('inventaris.perawatan.selesai');
        Route::delete('/sarpras/inventaris/{item}/pemeliharaan/{maintenance}', [InventoryController::class, 'maintenanceDestroy'])->name('inventaris.perawatan.destroy');
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

    // Surat Masuk/Keluar — CRUD + disposisi
    Route::middleware('role:super_admin|tata_usaha')->prefix('tu/surat')->name('surat.')->group(function () {
        Route::get('/', [LetterController::class, 'index'])->name('index');
        Route::get('/tambah', [LetterController::class, 'create'])->name('create');
        Route::post('/', [LetterController::class, 'store'])->name('store');
        Route::get('/{letter}', [LetterController::class, 'show'])->name('show');
        Route::get('/{letter}/edit', [LetterController::class, 'edit'])->name('edit');
        Route::put('/{letter}', [LetterController::class, 'update'])->name('update');
        Route::delete('/{letter}', [LetterController::class, 'destroy'])->name('destroy');
        Route::patch('/{letter}/disposisi', [LetterController::class, 'disposition'])->name('disposition');
    });
});
