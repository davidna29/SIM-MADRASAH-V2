<?php

use App\Http\Controllers\Api\V1\AgendaController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\GalleryController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\ScoreController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\TuitionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('api.request-id')
    ->group(function () {
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:api-login')
            ->name('login');

        Route::middleware(['auth:sanctum', 'active.api'])->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');

            Route::middleware('abilities:website:read')->group(function () {
                Route::get('dashboard/ringkasan', [DashboardController::class, 'index'])->name('dashboard.ringkasan');

                Route::get('siswa', [StudentController::class, 'index'])->name('siswa.index');
                Route::get('siswa/{student}', [StudentController::class, 'show'])->name('siswa.show');

                Route::get('jadwal', [ScheduleController::class, 'index'])->name('jadwal.index');
                Route::get('kehadiran-siswa', [AttendanceController::class, 'index'])->name('kehadiran.index');
                Route::get('nilai', [ScoreController::class, 'index'])->name('nilai.index');
                Route::get('spp/tagihan', [TuitionController::class, 'index'])->name('spp.tagihan');
                Route::get('pegawai', [EmployeeController::class, 'index'])->name('pegawai.index');

                Route::get('berita', [ArticleController::class, 'index'])->name('berita.index');
                Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index');
                Route::get('galeri', [GalleryController::class, 'index'])->name('galeri.index');
            });

            Route::middleware(['abilities:cms:write', 'throttle:api-write'])->prefix('cms')->name('cms.')->group(function () {
                Route::post('berita', [ArticleController::class, 'store'])
                    ->middleware('idempotency')
                    ->name('berita.store');
                Route::put('berita/{article}', [ArticleController::class, 'update'])
                    ->middleware('idempotency:optional')
                    ->name('berita.update');

                Route::post('agenda', [AgendaController::class, 'store'])
                    ->middleware('idempotency')
                    ->name('agenda.store');
                Route::put('agenda/{agenda}', [AgendaController::class, 'update'])
                    ->middleware('idempotency:optional')
                    ->name('agenda.update');

                Route::post('galeri', [GalleryController::class, 'store'])
                    ->middleware('idempotency')
                    ->name('galeri.store');
                Route::post('galeri/{album}/item', [GalleryController::class, 'storeItem'])
                    ->middleware('idempotency')
                    ->name('galeri.item.store');
                Route::put('galeri/{album}', [GalleryController::class, 'update'])
                    ->middleware('idempotency:optional')
                    ->name('galeri.update');
            });

            Route::middleware(['abilities:akademik:write', 'throttle:api-write'])->group(function () {
                Route::post('kehadiran-siswa', [AttendanceController::class, 'store'])
                    ->middleware('idempotency')
                    ->name('kehadiran.store');
                Route::post('nilai', [ScoreController::class, 'store'])
                    ->middleware('idempotency')
                    ->name('nilai.store');
            });

            Route::post('spp/bayar', [TuitionController::class, 'pay'])
                ->middleware(['abilities:keuangan:write', 'throttle:api-write', 'idempotency'])
                ->name('spp.bayar');
        });
    });
