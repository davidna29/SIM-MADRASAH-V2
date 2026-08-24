<?php

return [
    [
        'label' => 'Beranda Saya',
        'items' => [
            ['label' => 'Penugasan Mengajar', 'route' => 'guru.penugasan', 'icon' => 'clipboard-document-list', 'roles' => ['guru']],
            ['label' => 'Jurnal Mengajar', 'route' => 'guru.jurnal.index', 'icon' => 'clipboard-document-check', 'roles' => ['guru']],
            ['label' => 'Anak Saya', 'route' => 'ortu.dashboard', 'icon' => 'user-group', 'roles' => ['orang_tua']],
            ['label' => 'SPP Anak', 'route' => 'ortu.spp.index', 'icon' => 'banknotes', 'roles' => ['orang_tua']],
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
            [
                'label' => 'Jadwal Pelajaran',
                'icon' => 'calendar-days',
                'roles' => ['super_admin', 'wakamad_kurikulum', 'guru'],
                'children' => [
                    ['label' => 'Model Jadwal', 'route' => 'jadwal.model.index', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'wakamad_kurikulum']],
                    ['label' => 'Penyusunan', 'route' => 'jadwal.penyusunan', 'icon' => 'table-cells', 'roles' => ['super_admin', 'wakamad_kurikulum', 'guru']],
                ],
            ],
            [
                'label' => 'Jurnal Mengajar',
                'icon' => 'clipboard-document-check',
                'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah', 'guru', 'tata_usaha'],
                'children' => [
                    ['label' => 'Pantauan Jurnal', 'route' => 'jurnal.admin.index', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah']],
                    ['label' => 'Mingguan per Kelas', 'route' => 'jurnal.admin.mingguan', 'icon' => 'calendar-days', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah', 'guru', 'tata_usaha']],
                    ['label' => 'Mingguan per Guru', 'route' => 'jurnal.admin.mingguan.guru', 'icon' => 'user', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah', 'guru', 'tata_usaha']],
                ],
            ],
            ['label' => 'Rapor', 'route' => 'dashboard', 'icon' => 'document-text', 'roles' => ['super_admin', 'wakamad_kurikulum', 'wali_kelas', 'kepala_madrasah']],
        ],
    ],
    [
        'label' => 'Kesiswaan',
        'items' => [
            [
                'label' => 'Kehadiran Siswa',
                'icon' => 'clipboard-document-check',
                'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah', 'wakamad_kurikulum'],
                'children' => [
                    ['label' => 'Input Harian', 'route' => 'kehadiran.index', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah', 'wakamad_kurikulum']],
                    ['label' => 'Rekap Bulanan', 'route' => 'kehadiran.rekap', 'icon' => 'chart-bar', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah', 'wakamad_kurikulum']],
                ],
            ],
            ['label' => 'Prestasi & Pelanggaran', 'route' => 'dashboard', 'icon' => 'trophy', 'roles' => ['super_admin', 'wakamad_kesiswaan']],
            ['label' => 'Konseling (BK)', 'route' => 'dashboard', 'icon' => 'shield-check', 'roles' => ['super_admin', 'guru_bk']],
            ['label' => 'Ekstrakurikuler', 'route' => 'dashboard', 'icon' => 'star', 'roles' => ['super_admin', 'wakamad_kesiswaan']],
            ['label' => 'Portofolio Digital', 'route' => 'dashboard', 'icon' => 'document-arrow-up', 'roles' => ['*']],
        ],
    ],
    [
        'label' => 'Keuangan & TU',
        'items' => [
            [
                'label' => 'SPP Bulanan',
                'icon' => 'wallet',
                'roles' => ['super_admin', 'bendahara', 'tata_usaha', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Input & Rekap', 'route' => 'spp.index', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'bendahara', 'tata_usaha', 'kepala_madrasah']],
                    ['label' => 'Nominal SPP', 'route' => 'spp.settings', 'icon' => 'banknotes', 'roles' => ['super_admin', 'bendahara', 'tata_usaha']],
                    ['label' => 'Keringanan', 'route' => 'spp.overrides', 'icon' => 'scale', 'roles' => ['super_admin', 'bendahara', 'tata_usaha']],
                ],
            ],
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
