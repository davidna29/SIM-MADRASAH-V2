<?php

use App\Http\Controllers\Akademik\AttendanceController;
use App\Http\Controllers\Akademik\ClassGroupController;
use App\Http\Controllers\Akademik\HomeroomController;
use App\Http\Controllers\Akademik\JurnalController;
use App\Http\Controllers\Akademik\MutasiKeluarController;
use App\Http\Controllers\Akademik\ScheduleCellController;
use App\Http\Controllers\Akademik\ScheduleModelController;
use App\Http\Controllers\Akademik\StudentController;
use App\Http\Controllers\Akademik\SubjectController;
use App\Http\Controllers\Akademik\UjianPpi\ArsipController;
use App\Http\Controllers\Akademik\UjianPpi\GuruPpiController;
use App\Http\Controllers\Akademik\UjianPpi\KonfigurasiController;
use App\Http\Controllers\Akademik\UjianPpi\PeriodeController;
use App\Http\Controllers\Akademik\UjianPpi\PersiapanController;
use App\Http\Controllers\Akademik\UjianPpi\RekapController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cms\AgendaController;
use App\Http\Controllers\Cms\ArticleController;
use App\Http\Controllers\Cms\GalleryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Fondasi\AccountActivationController;
use App\Http\Controllers\Fondasi\JabatanController;
use App\Http\Controllers\Fondasi\PengaturanController;
use App\Http\Controllers\Fondasi\StrukturController;
use App\Http\Controllers\Fondasi\UnitKerjaController;
use App\Http\Controllers\Fondasi\UserController;
use App\Http\Controllers\Guru\JurnalController as GuruJurnalController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Kepegawaian\EmployeeAttendanceController;
use App\Http\Controllers\Kepegawaian\EmployeeController;
use App\Http\Controllers\Kesiswaan\AchievementController;
use App\Http\Controllers\Kesiswaan\CounselingController;
use App\Http\Controllers\Kesiswaan\ExtracurricularController;
use App\Http\Controllers\Kesiswaan\OffenseController;
use App\Http\Controllers\Kesiswaan\PortofolioController;
use App\Http\Controllers\Kesiswaan\PpiController;
use App\Http\Controllers\Kesiswaan\TahfidzController;
use App\Http\Controllers\Keuangan\TuitionController;
use App\Http\Controllers\Mutasi\AdminMutasiController;
use App\Http\Controllers\Mutasi\MutasiSettingController;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboardController;
use App\Http\Controllers\Ortu\SppController as OrtuSppController;
use App\Http\Controllers\Pemeliharaan\ActivityLogController;
use App\Http\Controllers\Pemeliharaan\BackupController;
use App\Http\Controllers\Pemeliharaan\LaporanController;
use App\Http\Controllers\Perpustakaan\LibraryController;
use App\Http\Controllers\Ppdb\AdminPpdbController;
use App\Http\Controllers\Ppdb\PpdbSettingController;
use App\Http\Controllers\Publik\AgendaController as PublikAgendaController;
use App\Http\Controllers\Publik\BeritaController as PublikBeritaController;
use App\Http\Controllers\Publik\GaleriController as PublikGaleriController;
use App\Http\Controllers\Publik\PindahanController;
use App\Http\Controllers\Publik\PpdbController;
use App\Http\Controllers\Sarpras\InventoryController;
use App\Http\Controllers\Sarpras\RoomController;
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

// PPDB Daring (publik, tanpa auth)
Route::get('/ppdb', [PpdbController::class, 'index'])->name('ppdb.form');
Route::post('/ppdb', [PpdbController::class, 'store'])->name('ppdb.store');
Route::get('/ppdb/sukses', [PpdbController::class, 'success'])->name('ppdb.success');
Route::post('/ppdb/minat', [PpdbController::class, 'interestStore'])->name('ppdb.interest.store');

// Siswa Pindah Masuk (Mutasi) — publik, tanpa auth
Route::get('/pindahan', [PindahanController::class, 'index'])->name('pindahan.form');
Route::post('/pindahan', [PindahanController::class, 'store'])->name('pindahan.store');
Route::get('/pindahan/sukses', [PindahanController::class, 'success'])->name('pindahan.success');
Route::post('/pindahan/minat', [PindahanController::class, 'interestStore'])->name('pindahan.interest.store');

// ============================================================
// Autentikasi
// ============================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    // Ganti password wajib (must_change_password) — lintas role
    Route::get('/ubah-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/ubah-password', [AuthController::class, 'updatePassword'])->name('password.update');

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
        Route::middleware('role:super_admin|tata_usaha|kepala_madrasah')->group(function () {
            Route::get('/kepegawaian/data-guru', [EmployeeController::class, 'index'])->name('pegawai.index');
            Route::get('/kepegawaian/data-guru/tambah', [EmployeeController::class, 'create'])->name('pegawai.create');
            Route::post('/kepegawaian/data-guru', [EmployeeController::class, 'store'])->name('pegawai.store');
            Route::get('/kepegawaian/data-guru/{employee}', [EmployeeController::class, 'show'])->name('pegawai.show');
            Route::get('/kepegawaian/data-guru/{employee}/edit', [EmployeeController::class, 'edit'])->name('pegawai.edit');
            Route::put('/kepegawaian/data-guru/{employee}', [EmployeeController::class, 'update'])->name('pegawai.update');
            Route::delete('/kepegawaian/data-guru/{employee}', [EmployeeController::class, 'destroy'])->name('pegawai.destroy');
        });

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

        // Backup & Restore — pemeliharaan
        Route::get('/pemeliharaan/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/pemeliharaan/backup/database', [BackupController::class, 'storeDb'])->name('backup.store-db');
        Route::post('/pemeliharaan/backup/files', [BackupController::class, 'storeFiles'])->name('backup.store-files');
        Route::get('/pemeliharaan/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download')->where('filename', '.+');
        Route::post('/pemeliharaan/backup/upload', [BackupController::class, 'upload'])->name('backup.upload');
        Route::post('/pemeliharaan/backup/restore', [BackupController::class, 'restore'])->name('backup.restore');
        Route::delete('/pemeliharaan/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy')->where('filename', '.+');

        // Pengguna & Role Management
        Route::get('/fondasi/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::get('/fondasi/pengguna/tambah', [UserController::class, 'create'])->name('pengguna.create');
        Route::post('/fondasi/pengguna', [UserController::class, 'store'])->name('pengguna.store');

        // Akun Menunggu Aktivasi — fixed routes BEFORE wildcard {user}
        Route::get('/fondasi/pengguna/aktivasi', [AccountActivationController::class, 'index'])->name('pengguna.aktivasi.index');
        Route::post('/fondasi/pengguna/aktivasi', [AccountActivationController::class, 'activate'])->name('pengguna.aktivasi.aktifkan');
        Route::get('/fondasi/pengguna/aktivasi/export', [AccountActivationController::class, 'exportCsv'])->name('pengguna.aktivasi.export');

        Route::get('/fondasi/pengguna/{user}', [UserController::class, 'show'])->name('pengguna.show');
        Route::get('/fondasi/pengguna/{user}/edit', [UserController::class, 'edit'])->name('pengguna.edit');
        Route::put('/fondasi/pengguna/{user}', [UserController::class, 'update'])->name('pengguna.update');
        Route::delete('/fondasi/pengguna/{user}', [UserController::class, 'destroy'])->name('pengguna.destroy');

        // Pengaturan Sistem
        Route::get('/fondasi/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/fondasi/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });

    // Struktur Organisasi — super_admin kelola; wakamad_kurikulum & kepala lihat
    Route::middleware('role:super_admin|wakamad_kurikulum|kepala_madrasah')->group(function () {
        // Unit Kerja
        Route::get('/fondasi/unit-kerja', [UnitKerjaController::class, 'index'])->name('unit-kerja.index');
        Route::get('/fondasi/unit-kerja/tambah', [UnitKerjaController::class, 'create'])->name('unit-kerja.create');
        Route::post('/fondasi/unit-kerja', [UnitKerjaController::class, 'store'])->name('unit-kerja.store');
        Route::get('/fondasi/unit-kerja/{unit}', [UnitKerjaController::class, 'show'])->name('unit-kerja.show');
        Route::get('/fondasi/unit-kerja/{unit}/edit', [UnitKerjaController::class, 'edit'])->name('unit-kerja.edit');
        Route::put('/fondasi/unit-kerja/{unit}', [UnitKerjaController::class, 'update'])->name('unit-kerja.update');
        Route::delete('/fondasi/unit-kerja/{unit}', [UnitKerjaController::class, 'destroy'])->name('unit-kerja.destroy');

        // Jabatan
        Route::get('/fondasi/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
        Route::get('/fondasi/jabatan/tambah', [JabatanController::class, 'create'])->name('jabatan.create');
        Route::post('/fondasi/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
        Route::get('/fondasi/jabatan/{position}', [JabatanController::class, 'show'])->name('jabatan.show');
        Route::get('/fondasi/jabatan/{position}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
        Route::put('/fondasi/jabatan/{position}', [JabatanController::class, 'update'])->name('jabatan.update');
        Route::delete('/fondasi/jabatan/{position}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');

        // Struktur Organisasi (read-only)
        Route::get('/fondasi/struktur', [StrukturController::class, 'index'])->name('struktur.index');
    });

    // PPDB Daring — admin (group multi-role, fixed routes BEFORE wildcard)
    Route::middleware('role:super_admin|tata_usaha|kepala_madrasah')->group(function () {
        Route::get('/ppdb/admin', [AdminPpdbController::class, 'index'])->name('ppdb.index');
        Route::get('/ppdb/admin/export', [AdminPpdbController::class, 'exportExcel'])->name('ppdb.export');

        // Pengaturan PPDB — saklar buka/tutup + konten landing (fixed routes BEFORE wildcard)
        Route::get('/ppdb/admin/pengaturan', [PpdbSettingController::class, 'index'])->name('ppdb.settings');
        Route::put('/ppdb/admin/pengaturan', [PpdbSettingController::class, 'update'])->name('ppdb.settings.update');
        Route::delete('/ppdb/admin/pengaturan/minat/{interest}', [PpdbSettingController::class, 'interestDestroy'])->name('ppdb.settings.interest.destroy');

        Route::get('/ppdb/admin/{registration}/edit', [AdminPpdbController::class, 'edit'])->name('ppdb.edit');
        Route::put('/ppdb/admin/{registration}', [AdminPpdbController::class, 'update'])->name('ppdb.update');
        Route::get('/ppdb/admin/{registration}', [AdminPpdbController::class, 'show'])->name('ppdb.show');
        Route::post('/ppdb/admin/{registration}/accept', [AdminPpdbController::class, 'accept'])->name('ppdb.accept');
        Route::post('/ppdb/admin/{registration}/reject', [AdminPpdbController::class, 'reject'])->name('ppdb.reject');
    });

    // Mutasi Masuk (Siswa Pindahan) — admin (fixed routes BEFORE wildcard)
    // Prefix di-relokasi ke bawah Data Siswa; nama route 'mutasi.*' tetap agar view/test tidak pecah.
    Route::middleware('role:super_admin|tata_usaha|kepala_madrasah')->prefix('akademik/mutasi-masuk/admin')->name('mutasi.')->group(function () {
        Route::get('/', [AdminMutasiController::class, 'index'])->name('index');

        // Pengaturan — fixed routes BEFORE wildcard
        Route::get('/pengaturan', [MutasiSettingController::class, 'index'])->name('settings');
        Route::put('/pengaturan', [MutasiSettingController::class, 'update'])->name('settings.update');
        Route::delete('/pengaturan/minat/{interest}', [MutasiSettingController::class, 'interestDestroy'])->name('settings.interest.destroy');

        Route::get('/{registration}/edit', [AdminMutasiController::class, 'edit'])->name('edit');
        Route::put('/{registration}', [AdminMutasiController::class, 'update'])->name('update');
        Route::get('/{registration}', [AdminMutasiController::class, 'show'])->name('show');
        Route::post('/{registration}/accept', [AdminMutasiController::class, 'accept'])->name('accept');
        Route::post('/{registration}/reject', [AdminMutasiController::class, 'reject'])->name('reject');
    });

    // Mutasi Siswa Keluar — admin (fixed routes BEFORE wildcard {mutation})
    Route::middleware('role:super_admin|tata_usaha|kepala_madrasah')->prefix('akademik/mutasi-keluar')->name('mutasi-keluar.')->group(function () {
        Route::get('/', [MutasiKeluarController::class, 'index'])->name('index');
        Route::get('/tambah', [MutasiKeluarController::class, 'create'])->name('create');
        Route::post('/', [MutasiKeluarController::class, 'store'])->name('store');
        Route::get('/{mutation}', [MutasiKeluarController::class, 'show'])->name('show');
        Route::get('/{mutation}/edit', [MutasiKeluarController::class, 'edit'])->name('edit');
        Route::put('/{mutation}', [MutasiKeluarController::class, 'update'])->name('update');
        Route::delete('/{mutation}', [MutasiKeluarController::class, 'destroy'])->name('destroy');
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

    // Ujian PPI Kelas VI (Munaqasah) — setup & konfigurasi (admin / wakamad kurikulum)
    Route::middleware('role:super_admin|wakamad_kurikulum')->prefix('akademik/ujian-ppi')->name('ujianppi.')->group(function () {
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show');
        Route::put('/periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');
        Route::post('/periode/{periode}/status', [PeriodeController::class, 'status'])->name('periode.status');
        Route::post('/periode/{periode}/salin-skala', [PeriodeController::class, 'copyScales'])->name('periode.salin-skala');
        Route::post('/periode/{periode}/kunci', [PeriodeController::class, 'kunci'])->name('periode.kunci');
        Route::post('/periode/{periode}/buka-kunci', [PeriodeController::class, 'bukaKunci'])->name('periode.buka-kunci');

        Route::get('/periode/{periode}/skala', [KonfigurasiController::class, 'skala'])->name('konfigurasi.skala');
        Route::post('/periode/{periode}/skala', [KonfigurasiController::class, 'skalaStore'])->name('konfigurasi.skala.store');
        Route::put('/periode/{periode}/skala/{scale}', [KonfigurasiController::class, 'skalaUpdate'])->name('konfigurasi.skala.update');
        Route::delete('/periode/{periode}/skala/{scale}', [KonfigurasiController::class, 'skalaDestroy'])->name('konfigurasi.skala.destroy');

        Route::get('/periode/{periode}/bobot', [KonfigurasiController::class, 'bobot'])->name('konfigurasi.bobot');
        Route::put('/periode/{periode}/bobot', [KonfigurasiController::class, 'bobotUpdate'])->name('konfigurasi.bobot.update');

        Route::get('/periode/{periode}/aspek', [KonfigurasiController::class, 'aspek'])->name('konfigurasi.aspek');
        Route::post('/periode/{periode}/aspek', [KonfigurasiController::class, 'aspekStore'])->name('konfigurasi.aspek.store');
        Route::put('/periode/{periode}/aspek/{category}', [KonfigurasiController::class, 'aspekUpdate'])->name('konfigurasi.aspek.update');
        Route::delete('/periode/{periode}/aspek/{category}', [KonfigurasiController::class, 'aspekDestroy'])->name('konfigurasi.aspek.destroy');
        Route::post('/periode/{periode}/aspek/{category}/item', [KonfigurasiController::class, 'aspekItemStore'])->name('konfigurasi.aspek.item.store');
        Route::put('/periode/{periode}/aspek/{category}/item/{aspect}', [KonfigurasiController::class, 'aspekItemUpdate'])->name('konfigurasi.aspek.item.update');
        Route::delete('/periode/{periode}/aspek/{category}/item/{aspect}', [KonfigurasiController::class, 'aspekItemDestroy'])->name('konfigurasi.aspek.item.destroy');

        Route::get('/periode/{periode}/hafalan', [KonfigurasiController::class, 'hafalan'])->name('konfigurasi.hafalan');
        Route::post('/periode/{periode}/hafalan', [KonfigurasiController::class, 'hafalanStore'])->name('konfigurasi.hafalan.store');
        Route::put('/periode/{periode}/hafalan/{materi}', [KonfigurasiController::class, 'hafalanUpdate'])->name('konfigurasi.hafalan.update');
        Route::delete('/periode/{periode}/hafalan/{materi}', [KonfigurasiController::class, 'hafalanDestroy'])->name('konfigurasi.hafalan.destroy');

        Route::get('/periode/{periode}/ruang', [PersiapanController::class, 'ruang'])->name('persiapan.ruang');
        Route::post('/periode/{periode}/ruang', [PersiapanController::class, 'ruangStore'])->name('persiapan.ruang.store');
        Route::put('/periode/{periode}/ruang/{room}', [PersiapanController::class, 'ruangUpdate'])->name('persiapan.ruang.update');
        Route::delete('/periode/{periode}/ruang/{room}', [PersiapanController::class, 'ruangDestroy'])->name('persiapan.ruang.destroy');
        Route::post('/periode/{periode}/ruang/copy', [PersiapanController::class, 'copyRooms'])->name('persiapan.ruang.copy');

        Route::get('/periode/{periode}/grup', [PersiapanController::class, 'grup'])->name('persiapan.grup');
        Route::post('/periode/{periode}/grup', [PersiapanController::class, 'grupStore'])->name('persiapan.grup.store');
        Route::put('/periode/{periode}/grup/{group}', [PersiapanController::class, 'grupUpdate'])->name('persiapan.grup.update');
        Route::delete('/periode/{periode}/grup/{group}', [PersiapanController::class, 'grupDestroy'])->name('persiapan.grup.destroy');
        Route::post('/periode/{periode}/grup/copy', [PersiapanController::class, 'copyGroups'])->name('persiapan.grup.copy');

        Route::get('/periode/{periode}/peserta', [PersiapanController::class, 'peserta'])->name('persiapan.peserta');
        Route::post('/periode/{periode}/peserta', [PersiapanController::class, 'pesertaAssign'])->name('persiapan.peserta.assign');
        Route::put('/periode/{periode}/peserta/{peserta}', [PersiapanController::class, 'pesertaUpdate'])->name('persiapan.peserta.update');
        Route::delete('/periode/{periode}/peserta/{peserta}', [PersiapanController::class, 'pesertaDestroy'])->name('persiapan.peserta.destroy');

        Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
        Route::get('/arsip/template', [ArsipController::class, 'template'])->name('arsip.template');
        Route::post('/arsip/preview', [ArsipController::class, 'preview'])->name('arsip.preview');
        Route::get('/arsip/preview', [ArsipController::class, 'previewShow'])->name('arsip.previewShow');
        Route::post('/arsip/simpan', [ArsipController::class, 'simpan'])->name('arsip.simpan');
        Route::post('/arsip/batal', [ArsipController::class, 'batal'])->name('arsip.batal');
    });

    // Ujian PPI — Rekap Kelas VI (admin/wakamad edit; kepala read-only)
    Route::middleware('role:super_admin|wakamad_kurikulum|kepala_madrasah')->prefix('akademik/ujian-ppi')->name('ujianppi.')->group(function () {
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/{periode}/pdf', [RekapController::class, 'pdf'])->name('rekap.pdf');
        Route::get('/rekap/{periode}/excel', [RekapController::class, 'excel'])->name('rekap.excel');
        Route::post('/rekap/{periode}/peserta/{peserta}/koreksi', [RekapController::class, 'koreksi'])->name('rekap.koreksi');
    });

    // Ujian PPI — input nilai guru (penguji & pembimbing) + dokumen
    Route::middleware('role:super_admin|wakamad_kurikulum|guru')->prefix('akademik/ujian-ppi')->name('ujianppi.')->group(function () {
        Route::get('/mulai', [GuruPpiController::class, 'index'])->name('guru.index');
        Route::get('/{periode}/ujian', [GuruPpiController::class, 'ujian'])->name('guru.ujian');
        Route::post('/{periode}/ujian/{peserta}', [GuruPpiController::class, 'ujianStore'])->name('guru.ujian.store');
        Route::get('/{periode}/setoran', [GuruPpiController::class, 'setoran'])->name('guru.setoran');
        Route::post('/{periode}/setoran/{peserta}', [GuruPpiController::class, 'setoranStore'])->name('guru.setoran.store');
        Route::get('/{periode}/teks', [GuruPpiController::class, 'teks'])->name('guru.teks');
        Route::get('/{periode}/teks/{peserta}/pdf', [GuruPpiController::class, 'teksPdf'])->name('guru.teks.pdf');
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

        // PPI (Praktek Pengamalan Ibadah)
        Route::get('/kesiswaan/ppi', [PpiController::class, 'index'])->name('ppi.index');
        Route::get('/kesiswaan/ppi/konfigurasi', [PpiController::class, 'konfigurasi'])->name('ppi.konfigurasi');
        Route::post('/kesiswaan/ppi/konfigurasi', [PpiController::class, 'konfigurasiUpdate'])->name('ppi.konfigurasi.update');
        Route::get('/kesiswaan/ppi/{siswa}/input', [PpiController::class, 'input'])->name('ppi.input');
        Route::post('/kesiswaan/ppi/{siswa}/input', [PpiController::class, 'store'])->name('ppi.store');
        Route::get('/kesiswaan/ppi/{siswa}/cetak', [PpiController::class, 'cetak'])->name('ppi.cetak');
        Route::get('/kesiswaan/ppi/{siswa}/cetak/pdf', [PpiController::class, 'cetakPdf'])->name('ppi.cetak.pdf');
        Route::get('/kesiswaan/ppi/{siswa}/cetak/excel', [PpiController::class, 'cetakExcel'])->name('ppi.cetak.excel');

        // Tahfidz
        Route::get('/kesiswaan/tahfidz', [TahfidzController::class, 'index'])->name('tahfidz.index');
        Route::get('/kesiswaan/tahfidz/konfigurasi', [TahfidzController::class, 'konfigurasi'])->name('tahfidz.konfigurasi');
        Route::post('/kesiswaan/tahfidz/konfigurasi', [TahfidzController::class, 'konfigurasiUpdate'])->name('tahfidz.konfigurasi.update');
        Route::get('/kesiswaan/tahfidz/{siswa}/input', [TahfidzController::class, 'input'])->name('tahfidz.input');
        Route::post('/kesiswaan/tahfidz/{siswa}/input', [TahfidzController::class, 'store'])->name('tahfidz.store');
        Route::get('/kesiswaan/tahfidz/{siswa}/cetak', [TahfidzController::class, 'cetak'])->name('tahfidz.cetak');
        Route::get('/kesiswaan/tahfidz/{siswa}/cetak/pdf', [TahfidzController::class, 'cetakPdf'])->name('tahfidz.cetak.pdf');
        Route::get('/kesiswaan/tahfidz/{siswa}/cetak/excel', [TahfidzController::class, 'cetakExcel'])->name('tahfidz.cetak.excel');

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

    // Ruangan & Laboratorium (Sarpras) — CRUD, kepala_madrasah read-only
    Route::middleware('role:super_admin|wakamad_sarpras|tata_usaha|kepala_madrasah')->prefix('sarpras/ruangan')->name('ruangan.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');

        // Fixed routes SEBELUM wildcard {room}
        Route::get('/tambah', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');

        Route::get('/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
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

    // Kehadiran Guru & Pegawai — input harian oleh TU / Super Admin; kepala_madrasah read-only
    Route::middleware('role:super_admin|tata_usaha|kepala_madrasah')->prefix('kepegawaian/kehadiran')->name('pegawai.kehadiran.')->group(function () {
        Route::get('/', [EmployeeAttendanceController::class, 'index'])->name('index');
        Route::post('/', [EmployeeAttendanceController::class, 'store'])->name('store');
        Route::get('/rekap-bulanan', [EmployeeAttendanceController::class, 'rekapBulanan'])->name('rekap-bulanan');
        Route::get('/rekap-tahunan', [EmployeeAttendanceController::class, 'rekapTahunan'])->name('rekap-tahunan');
    });
});
