<?php

return [
    [
        'label' => 'Beranda Saya',
        'items' => [
            ['label' => 'Penugasan Mengajar', 'route' => 'guru.penugasan', 'icon' => 'clipboard-document-list', 'roles' => ['guru']],
            ['label' => 'Anak Saya', 'route' => 'ortu.dashboard', 'icon' => 'user-group', 'roles' => ['orang_tua']],
        ],
    ],
    [
        'label' => 'Fondasi & Pengaturan',
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'roles' => ['*']],
            ['label' => 'Pengguna & Role', 'route' => 'dashboard', 'icon' => 'user-group', 'roles' => ['super_admin']],
            ['label' => 'Struktur Organisasi', 'route' => 'dashboard', 'icon' => 'building-library', 'roles' => ['super_admin']],
            ['label' => 'Pengaturan Sistem', 'route' => 'dashboard', 'icon' => 'cog-6-tooth', 'roles' => ['super_admin']],
        ],
    ],
    [
        'label' => 'Akademik',
        'items' => [
            ['label' => 'Data Siswa', 'route' => 'siswa.index', 'icon' => 'academic-cap', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'tata_usaha', 'wali_kelas', 'kepala_madrasah']],
            ['label' => 'Data Guru & Pegawai', 'route' => 'pegawai.index', 'icon' => 'user-group', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
            ['label' => 'Mata Pelajaran', 'route' => 'mapel.index', 'icon' => 'book-open', 'roles' => ['super_admin', 'wakamad_kurikulum']],
            ['label' => 'Kelas & Penempatan', 'route' => 'kelas.index', 'icon' => 'building-library', 'roles' => ['super_admin', 'wakamad_kurikulum']],
            ['label' => 'Jadwal Mengajar', 'route' => 'dashboard', 'icon' => 'calendar-days', 'roles' => ['super_admin', 'wakamad_kurikulum', 'guru']],
            ['label' => 'Jurnal & Penilaian', 'route' => 'dashboard', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'wakamad_kurikulum', 'guru']],
            ['label' => 'Rapor', 'route' => 'dashboard', 'icon' => 'document-text', 'roles' => ['super_admin', 'wakamad_kurikulum', 'wali_kelas', 'kepala_madrasah']],
        ],
    ],
    [
        'label' => 'Kesiswaan',
        'items' => [
            ['label' => 'Kehadiran Siswa', 'route' => 'dashboard', 'icon' => 'clipboard-document-check', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru']],
            ['label' => 'Prestasi & Pelanggaran', 'route' => 'dashboard', 'icon' => 'trophy', 'roles' => ['super_admin', 'wakamad_kesiswaan']],
            ['label' => 'Konseling (BK)', 'route' => 'dashboard', 'icon' => 'shield-check', 'roles' => ['super_admin', 'guru_bk']],
            ['label' => 'Ekstrakurikuler', 'route' => 'dashboard', 'icon' => 'star', 'roles' => ['super_admin', 'wakamad_kesiswaan']],
            ['label' => 'Portofolio Digital', 'route' => 'dashboard', 'icon' => 'document-arrow-up', 'roles' => ['*']],
        ],
    ],
    [
        'label' => 'Keuangan & TU',
        'items' => [
            ['label' => 'Tagihan & Pembayaran', 'route' => 'dashboard', 'icon' => 'banknotes', 'roles' => ['super_admin', 'bendahara', 'kepala_madrasah']],
            ['label' => 'Rekap Keuangan', 'route' => 'dashboard', 'icon' => 'chart-bar', 'roles' => ['super_admin', 'bendahara', 'kepala_madrasah']],
            ['label' => 'Surat Masuk / Keluar', 'route' => 'dashboard', 'icon' => 'envelope', 'roles' => ['super_admin', 'tata_usaha']],
            ['label' => 'Arsip & Dokumen', 'route' => 'dashboard', 'icon' => 'archive-box', 'roles' => ['super_admin', 'tata_usaha']],
        ],
    ],
    [
        'label' => 'Sarpras & Perpustakaan',
        'items' => [
            ['label' => 'Inventaris Barang', 'route' => 'dashboard', 'icon' => 'square-2-stack', 'roles' => ['super_admin', 'wakamad_sarpras']],
            ['label' => 'Ruangan & Lab', 'route' => 'dashboard', 'icon' => 'building-library', 'roles' => ['super_admin', 'wakamad_sarpras']],
            ['label' => 'Perpustakaan', 'route' => 'dashboard', 'icon' => 'book-open', 'roles' => ['super_admin', 'pustakawan']],
        ],
    ],
    [
        'label' => 'Mutu & Akreditasi',
        'items' => [
            ['label' => 'PKKM Center', 'route' => 'dashboard', 'icon' => 'shield-check', 'roles' => ['super_admin', 'kepala_madrasah']],
            ['label' => 'Akreditasi (8 SNP)', 'route' => 'dashboard', 'icon' => 'flag', 'roles' => ['super_admin', 'kepala_madrasah']],
            ['label' => 'Rencana Kerja Madrasah', 'route' => 'dashboard', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'kepala_madrasah']],
        ],
    ],
    [
        'label' => 'Publikasi & PPDB',
        'items' => [
            ['label' => 'Berita & Agenda', 'route' => 'dashboard', 'icon' => 'megaphone', 'roles' => ['super_admin', 'wakamad_humas', 'editor_berita']],
            ['label' => 'PPDB Daring', 'route' => 'dashboard', 'icon' => 'user-plus', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
            ['label' => 'Website Publik', 'route' => 'dashboard', 'icon' => 'globe-alt', 'roles' => ['super_admin', 'wakamad_humas', 'editor_berita']],
        ],
    ],
    [
        'label' => 'Pemeliharaan',
        'items' => [
            ['label' => 'Pusat Laporan', 'route' => 'dashboard', 'icon' => 'chart-bar', 'roles' => ['*']],
            ['label' => 'Pusat Dokumen', 'route' => 'dashboard', 'icon' => 'folder-open', 'roles' => ['*']],
            ['label' => 'Activity & Audit Log', 'route' => 'dashboard', 'icon' => 'arrow-path', 'roles' => ['super_admin']],
            ['label' => 'Backup & Restore', 'route' => 'dashboard', 'icon' => 'archive-box-arrow-down', 'roles' => ['super_admin']],
        ],
    ],
];
