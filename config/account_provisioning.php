<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Status terminal pegawai — akun dinonaktifkan otomatis
    |--------------------------------------------------------------------------
    | Status yang membuat akun user mati. 'cuti' TIDAK termasuk — cuti tetap aktif.
    */
    'employee_terminal_statuses' => ['nonaktif'],

    /*
    |--------------------------------------------------------------------------
    | Status terminal enrollment siswa — akun dinonaktifkan otomatis
    |--------------------------------------------------------------------------
    | Status penempatan pada tahun ajaran berjalan yang membuat akun user siswa mati.
    */
    'student_enrollment_terminal_statuses' => ['keluar', 'alumni'],
];
