<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Akademik\AttendanceController;
use App\Http\Controllers\Akademik\ClassGroupController;
use App\Http\Controllers\Akademik\ScheduleController;
use App\Http\Controllers\Akademik\StudentController;
use App\Http\Controllers\Akademik\SubjectController;
use App\Http\Controllers\Akademik\TeacherAssignmentController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Kepegawaian\EmployeeController;
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

        // Modul Penugasan Mengajar
        Route::get('/akademik/penugasan-mengajar', [TeacherAssignmentController::class, 'index'])->name('penugasan.index');
        Route::get('/akademik/penugasan-mengajar/tambah', [TeacherAssignmentController::class, 'create'])->name('penugasan.create');
        Route::post('/akademik/penugasan-mengajar', [TeacherAssignmentController::class, 'store'])->name('penugasan.store');
        Route::get('/akademik/penugasan-mengajar/{assignment}/edit', [TeacherAssignmentController::class, 'edit'])->name('penugasan.edit');
        Route::put('/akademik/penugasan-mengajar/{assignment}', [TeacherAssignmentController::class, 'update'])->name('penugasan.update');
        Route::delete('/akademik/penugasan-mengajar/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('penugasan.destroy');

        // Modul Kehadiran Siswa
        Route::get('/kesiswaan/kehadiran', [AttendanceController::class, 'index'])->name('kehadiran.index');
        Route::post('/kesiswaan/kehadiran', [AttendanceController::class, 'store'])->name('kehadiran.store');

        // Modul Jadwal Mengajar
        Route::get('/akademik/jadwal-mengajar', [ScheduleController::class, 'index'])->name('jadwal.index');
        Route::get('/akademik/jadwal-mengajar/tambah', [ScheduleController::class, 'create'])->name('jadwal.create');
        Route::post('/akademik/jadwal-mengajar', [ScheduleController::class, 'store'])->name('jadwal.store');
        Route::get('/akademik/jadwal-mengajar/{schedule}/edit', [ScheduleController::class, 'edit'])->name('jadwal.edit');
        Route::put('/akademik/jadwal-mengajar/{schedule}', [ScheduleController::class, 'update'])->name('jadwal.update');
        Route::delete('/akademik/jadwal-mengajar/{schedule}', [ScheduleController::class, 'destroy'])->name('jadwal.destroy');
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
