<?php

use Illuminate\Support\Facades\Route;
use App\Support\DemoData;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::view('/login', 'pages.auth.login')->name('login');

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
