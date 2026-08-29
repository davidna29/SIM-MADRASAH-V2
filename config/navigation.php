<?php

return [
    [
        'label' => 'Beranda Saya',
        'icon' => 'home',
        'items' => [
            ['label' => 'Penugasan Mengajar', 'route' => 'guru.penugasan', 'icon' => 'clipboard-document-list', 'roles' => ['guru']],
            ['label' => 'Jurnal Mengajar', 'route' => 'guru.jurnal.index', 'icon' => 'clipboard-document-check', 'roles' => ['guru']],
            ['label' => 'Anak Saya', 'route' => 'ortu.dashboard', 'icon' => 'user-group', 'roles' => ['orang_tua']],
            ['label' => 'SPP Anak', 'route' => 'ortu.spp.index', 'icon' => 'banknotes', 'roles' => ['orang_tua']],
        ],
    ],
    [
        'label' => 'Portal Siswa',
        'icon' => 'user',
        'items' => [
            ['label' => 'Data Saya', 'route' => 'siswa.dashboard', 'icon' => 'user', 'roles' => ['siswa']],
        ],
    ],
    [
        'label' => 'Sistem',
        'icon' => 'cog-6-tooth',
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'roles' => ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan', 'bendahara', 'tata_usaha']],
            [
                'label' => 'Pengguna & Role',
                'icon' => 'user-group',
                'roles' => ['super_admin'],
                'children' => [
                    ['label' => 'Pengguna & Role', 'route' => 'pengguna.index', 'icon' => 'user-group', 'roles' => ['super_admin']],
                    ['label' => 'Akun Menunggu Aktivasi', 'route' => 'pengguna.aktivasi.index', 'icon' => 'user-plus', 'roles' => ['super_admin']],
                ],
            ],
            [
                'label' => 'Struktur Organisasi',
                'icon' => 'building-library',
                'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Unit Kerja', 'route' => 'unit-kerja.index', 'icon' => 'building-library', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah']],
                    ['label' => 'Jabatan', 'route' => 'jabatan.index', 'icon' => 'briefcase', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah']],
                    ['label' => 'Struktur', 'route' => 'struktur.index', 'icon' => 'users', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah']],
                ],
            ],
            ['label' => 'Pengaturan Sistem', 'route' => 'pengaturan.index', 'icon' => 'cog-6-tooth', 'roles' => ['super_admin']],
        ],
    ],
    [
        'label' => 'Akademik',
        'icon' => 'book-open',
        'items' => [
            [
                'label' => 'Data Siswa',
                'icon' => 'academic-cap',
                'roles' => ['super_admin', 'wakamad_kesiswaan', 'tata_usaha', 'wali_kelas', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Data Siswa', 'route' => 'siswa.index', 'icon' => 'user-group', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'tata_usaha', 'wali_kelas', 'kepala_madrasah']],
                    ['label' => 'Mutasi Siswa Masuk', 'route' => 'mutasi.index', 'icon' => 'arrow-right-start-on-rectangle', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
                    ['label' => 'Mutasi Siswa Keluar', 'route' => 'mutasi-keluar.index', 'icon' => 'arrow-right-end-on-rectangle', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
                ],
            ],
            ['label' => 'Data Guru & Pegawai', 'route' => 'pegawai.index', 'icon' => 'user-group', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
            ['label' => 'Kehadiran Guru & Pegawai', 'route' => 'pegawai.kehadiran.index', 'icon' => 'clipboard-document-check', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
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
            ['label' => 'Rapor', 'route' => 'dashboard', 'icon' => 'document-text', 'roles' => ['super_admin', 'wakamad_kurikulum', 'wali_kelas', 'kepala_madrasah'], 'placeholder' => true],
            [
                'label' => 'Ujian PPI',
                'icon' => 'clipboard-document-check',
                'roles' => ['super_admin', 'wakamad_kurikulum', 'guru', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Periode Ujian', 'route' => 'ujianppi.periode.index', 'icon' => 'calendar-days', 'roles' => ['super_admin', 'wakamad_kurikulum']],
                    ['label' => 'Beranda Guru Ujian', 'route' => 'ujianppi.guru.index', 'icon' => 'pencil-square', 'roles' => ['super_admin', 'wakamad_kurikulum', 'guru']],
                    ['label' => 'Rekap Kelas VI', 'route' => 'ujianppi.rekap.index', 'icon' => 'table-cells', 'roles' => ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah']],
                    ['label' => 'Arsip', 'route' => 'ujianppi.arsip.index', 'icon' => 'archive-box', 'roles' => ['super_admin', 'wakamad_kurikulum']],
                ],
            ],
        ],
    ],
    [
        'label' => 'Kesiswaan',
        'icon' => 'user-group',
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
            [
                'label' => 'Prestasi & Pelanggaran',
                'icon' => 'trophy',
                'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'guru_bk', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Prestasi', 'route' => 'prestasi.index', 'icon' => 'trophy', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah']],
                    ['label' => 'Pelanggaran', 'route' => 'pelanggaran.index', 'icon' => 'shield-exclamation', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru_bk', 'kepala_madrasah']],
                ],
            ],
            [
                'label' => 'PPI (Pembiasaan)',
                'icon' => 'heart',
                'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Input Nilai', 'route' => 'ppi.index', 'icon' => 'pencil-square', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah']],
                    ['label' => 'Konfigurasi Materi', 'route' => 'ppi.konfigurasi', 'icon' => 'cog-6-tooth', 'roles' => ['super_admin', 'wakamad_kesiswaan']],
                ],
            ],
            [
                'label' => 'Tahfidz',
                'icon' => 'book-open',
                'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Input Nilai', 'route' => 'tahfidz.index', 'icon' => 'pencil-square', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah']],
                    ['label' => 'Konfigurasi Materi', 'route' => 'tahfidz.konfigurasi', 'icon' => 'cog-6-tooth', 'roles' => ['super_admin', 'wakamad_kesiswaan']],
                ],
            ],
            ['label' => 'Konseling (BK)', 'route' => 'konseling.index', 'icon' => 'shield-check', 'roles' => ['super_admin', 'guru_bk', 'kepala_madrasah']],
            ['label' => 'Ekstrakurikuler', 'route' => 'ekskul.index', 'icon' => 'star', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'guru', 'wali_kelas', 'kepala_madrasah']],
            ['label' => 'Portofolio Digital', 'route' => 'portofolio.index', 'icon' => 'document-arrow-up', 'roles' => ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru_bk', 'kepala_madrasah']],
        ],
    ],
    [
        'label' => 'Keuangan & TU',
        'icon' => 'banknotes',
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
            ['label' => 'Rekap Keuangan', 'route' => 'dashboard', 'icon' => 'chart-bar', 'roles' => ['super_admin', 'bendahara', 'kepala_madrasah'], 'placeholder' => true],
            [
                'label' => 'Surat Masuk / Keluar',
                'icon' => 'envelope',
                'roles' => ['super_admin', 'tata_usaha'],
                'children' => [
                    ['label' => 'Surat Masuk', 'route' => 'surat.index', 'routeParams' => ['type' => 'masuk'], 'icon' => 'inbox-arrow-down', 'roles' => ['super_admin', 'tata_usaha']],
                    ['label' => 'Surat Keluar', 'route' => 'surat.index', 'routeParams' => ['type' => 'keluar'], 'icon' => 'paper-airplane', 'roles' => ['super_admin', 'tata_usaha']],
                ],
            ],
            ['label' => 'Arsip & Dokumen', 'route' => 'dashboard', 'icon' => 'archive-box', 'roles' => ['super_admin', 'tata_usaha'], 'placeholder' => true],
        ],
    ],
    [
        'label' => 'Sarpras & Perpustakaan',
        'icon' => 'building-library',
        'items' => [
            [
                'label' => 'Inventaris Barang',
                'icon' => 'square-2-stack',
                'roles' => ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Daftar Barang', 'route' => 'inventaris.index', 'icon' => 'square-2-stack', 'roles' => ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah']],
                    ['label' => 'Kategori', 'route' => 'inventaris.kategori.index', 'icon' => 'tag', 'roles' => ['super_admin', 'wakamad_sarpras']],
                ],
            ],
            [
                'label' => 'Ruangan & Lab',
                'icon' => 'building-library',
                'roles' => ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Daftar Ruangan', 'route' => 'ruangan.index', 'icon' => 'building-library', 'roles' => ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah']],
                    ['label' => 'Laboratorium', 'route' => 'ruangan.index', 'routeParams' => ['type' => 'laboratorium'], 'icon' => 'beaker', 'roles' => ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah']],
                ],
            ],
            [
                'label' => 'Perpustakaan',
                'icon' => 'book-open',
                'roles' => ['super_admin', 'pustakawan', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Katalog Buku', 'route' => 'perpustakaan.index', 'icon' => 'book-open', 'roles' => ['super_admin', 'pustakawan', 'kepala_madrasah']],
                    ['label' => 'Anggota', 'route' => 'perpustakaan.anggota.index', 'icon' => 'user-group', 'roles' => ['super_admin', 'pustakawan']],
                    ['label' => 'Kategori', 'route' => 'perpustakaan.kategori.index', 'icon' => 'tag', 'roles' => ['super_admin', 'pustakawan']],
                ],
            ],
        ],
    ],
    [
        'label' => 'Mutu & Akreditasi',
        'icon' => 'shield-check',
        'items' => [
            ['label' => 'PKKM Center', 'route' => 'dashboard', 'icon' => 'shield-check', 'roles' => ['super_admin', 'kepala_madrasah'], 'placeholder' => true],
            ['label' => 'Akreditasi (8 SNP)', 'route' => 'dashboard', 'icon' => 'flag', 'roles' => ['super_admin', 'kepala_madrasah'], 'placeholder' => true],
            ['label' => 'Rencana Kerja Madrasah', 'route' => 'dashboard', 'icon' => 'clipboard-document-list', 'roles' => ['super_admin', 'kepala_madrasah'], 'placeholder' => true],
        ],
    ],
    [
        'label' => 'Publikasi',
        'icon' => 'megaphone',
        'items' => [
            [
                'label' => 'Berita & Agenda',
                'icon' => 'megaphone',
                'roles' => ['super_admin', 'wakamad_humas', 'editor_berita', 'kepala_madrasah', 'tata_usaha', 'guru'],
                'children' => [
                    ['label' => 'Kelola Berita', 'route' => 'cms.berita.index', 'icon' => 'newspaper', 'roles' => ['super_admin', 'wakamad_humas', 'editor_berita', 'kepala_madrasah', 'tata_usaha', 'guru']],
                    ['label' => 'Agenda & Pengumuman', 'route' => 'cms.agenda.index', 'icon' => 'calendar-days', 'roles' => ['super_admin', 'wakamad_humas', 'editor_berita', 'tata_usaha', 'kepala_madrasah']],
                    ['label' => 'Galeri', 'route' => 'cms.galeri.index', 'icon' => 'photo', 'roles' => ['super_admin', 'wakamad_humas', 'editor_berita', 'kepala_madrasah', 'tata_usaha']],
                    ['label' => 'Website Publik', 'route' => 'publik.berita.index', 'icon' => 'globe-alt', 'roles' => ['*'], 'external' => true],
                ],
            ],
            [
                'label' => 'PPDB Daring',
                'icon' => 'user-plus',
                'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah'],
                'children' => [
                    ['label' => 'Pendaftar', 'route' => 'ppdb.index', 'icon' => 'user-plus', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
                    ['label' => 'Pengaturan PPDB', 'route' => 'ppdb.settings', 'icon' => 'cog-6-tooth', 'roles' => ['super_admin', 'tata_usaha', 'kepala_madrasah']],
                ],
            ],
        ],
    ],
    [
        'label' => 'Pemeliharaan Sistem',
        'icon' => 'wrench-screwdriver',
        'items' => [
            ['label' => 'Pusat Laporan', 'route' => 'laporan.index', 'icon' => 'chart-bar', 'roles' => ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan', 'bendahara']],
            ['label' => 'Pusat Dokumen', 'route' => 'dashboard', 'icon' => 'folder-open', 'roles' => ['*'], 'placeholder' => true],
            ['label' => 'Activity & Audit Log', 'route' => 'activity-log.index', 'icon' => 'arrow-path', 'roles' => ['super_admin']],
            ['label' => 'Backup & Restore', 'route' => 'backup.index', 'icon' => 'archive-box-arrow-down', 'roles' => ['super_admin']],
        ],
    ],
];
