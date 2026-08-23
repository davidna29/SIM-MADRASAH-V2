<?php

namespace App\Support;

class DemoData
{
    public static function siswa(): array
    {
        return [
            ['nis' => '240101', 'nama' => 'Aisyah Nur Azizah', 'kelas' => 'I-A', 'jk' => 'P', 'status' => 'aktif'],
            ['nis' => '240102', 'nama' => 'Bilal Ramadhan', 'kelas' => 'I-A', 'jk' => 'L', 'status' => 'aktif'],
            ['nis' => '240103', 'nama' => 'Cinta Lestari Putri', 'kelas' => 'I-A', 'jk' => 'P', 'status' => 'aktif'],
            ['nis' => '240104', 'nama' => 'Dimas Prasetyo', 'kelas' => 'I-A', 'jk' => 'L', 'status' => 'aktif'],
            ['nis' => '240105', 'nama' => 'Eka Salsabila', 'kelas' => 'I-A', 'jk' => 'P', 'status' => 'aktif'],
            ['nis' => '239901', 'nama' => 'Fathir Rahman', 'kelas' => 'VI-A', 'jk' => 'L', 'status' => 'aktif'],
            ['nis' => '239902', 'nama' => 'Ghina Aulia Rahma', 'kelas' => 'VI-A', 'jk' => 'P', 'status' => 'aktif'],
            ['nis' => '239903', 'nama' => 'Hafizh Akbar', 'kelas' => 'VI-A', 'jk' => 'L', 'status' => 'aktif'],
            ['nis' => '239904', 'nama' => 'Intan Permatasari', 'kelas' => 'VI-A', 'jk' => 'P', 'status' => 'aktif'],
            ['nis' => '239905', 'nama' => 'Jaka Setiawan', 'kelas' => 'VI-A', 'jk' => 'L', 'status' => 'aktif'],
            ['nis' => '238101', 'nama' => 'Khalifah Nur Hidayah', 'kelas' => 'II-A', 'jk' => 'P', 'status' => 'aktif'],
            ['nis' => '238102', 'nama' => 'Lukman Hakim', 'kelas' => 'II-A', 'jk' => 'L', 'status' => 'aktif'],
        ];
    }

    public static function tagihan(): array
    {
        return [
            ['nis' => '240101', 'nama' => 'Aisyah Nur Azizah', 'jenis' => 'SPP Agustus', 'nominal' => 250000, 'status' => 'lunas', 'tanggal' => '2026-08-05'],
            ['nis' => '240102', 'nama' => 'Bilal Ramadhan', 'jenis' => 'SPP Agustus', 'nominal' => 250000, 'status' => 'cicilan', 'tanggal' => '2026-08-06'],
            ['nis' => '240103', 'nama' => 'Cinta Lestari Putri', 'jenis' => 'SPP Agustus', 'nominal' => 250000, 'status' => 'belum', 'tanggal' => '2026-08-01'],
            ['nis' => '239901', 'nama' => 'Fathir Rahman', 'jenis' => 'SPP Agustus', 'nominal' => 250000, 'status' => 'lunas', 'tanggal' => '2026-08-03'],
            ['nis' => '238101', 'nama' => 'Khalifah Nur Hidayah', 'jenis' => 'SPP Agustus', 'nominal' => 250000, 'status' => 'lunas', 'tanggal' => '2026-08-04'],
            ['nis' => '239902', 'nama' => 'Ghina Aulia Rahma', 'jenis' => 'SPP Agustus', 'nominal' => 250000, 'status' => 'cicilan', 'tanggal' => '2026-08-07'],
        ];
    }

    public static function perluTindakan(): array
    {
        return [
            ['id' => 'P-0042', 'label' => 'Persetujuan rapor semester genap Kelas VII', 'jenis' => 'Nilai', 'waktu' => 'Menunggu sejak 22 Agu', 'urgensi' => 'tinggi'],
            ['id' => 'P-0041', 'label' => 'Berita "Kegiatan MPLS 2026" menunggu terbit', 'jenis' => 'Berita', 'waktu' => 'Draft oleh Wakamad Humas', 'urgensi' => 'sedang'],
            ['id' => 'P-0040', 'label' => 'Verifikasi 3 tagihan SPP belum lunas', 'jenis' => 'Keuangan', 'waktu' => 'Jatuh tempo 25 Agu', 'urgensi' => 'tinggi'],
            ['id' => 'P-0039', 'label' => 'Eviden PKKM standar 4 komponen 1', 'jenis' => 'PKKM', 'waktu' => 'Perlu verifikasi', 'urgensi' => 'sedang'],
            ['id' => 'P-0038', 'label' => 'Pengajuan surat keluar — undangan rapat', 'jenis' => 'TU', 'waktu' => 'Draft oleh Tata Usaha', 'urgensi' => 'rendah'],
        ];
    }

    public static function pengumuman(): array
    {
        return [
            ['label' => 'Libur peringatan Maulid Nabi', 'tanggal' => '16 Sep 2026', 'jenis' => 'agenda'],
            ['label' => 'Rapat dewan guru — rapor semester genap', 'tanggal' => '25 Agu 2026', 'jenis' => 'agenda'],
            ['label' => 'Pembagian rapor & penyerahan berkas', 'tanggal' => '27 Agu 2026', 'jenis' => 'agenda'],
            ['label' => 'Asesmen sumatif semester ganjil dimulai', 'tanggal' => '01 Sep 2026', 'jenis' => 'agenda'],
            ['label' => 'Penerimaan PPDB gelombang 2 dibuka', 'tanggal' => '10 Sep 2026', 'jenis' => 'pengumuman'],
        ];
    }

    public static function aktivitas(): array
    {
        return [
            ['nama' => 'Admin Madrasah', 'aksi' => 'menyetujui nilai rapor Kelas VII-A', 'waktu' => '5 menit lalu'],
            ['nama' => 'Bu. Ratna (Bendahara)', 'aksi' => 'mencatat pembayaran SPP Aisyah Nur Azizah', 'waktu' => '12 menit lalu'],
            ['nama' => 'Pak. Umar (Wakamad)', 'aksi' => 'memublikasikan berita MPLS 2026', 'waktu' => '1 jam lalu'],
            ['nama' => 'Bu. Sari (TU)', 'aksi' => 'mengunggah arsip surat masuk No. 042/SM/2026', 'waktu' => '2 jam lalu'],
        ];
    }

    public static function pegawai(): array
    {
        return [
            [
                'nip' => '198503122010011003', 'nik' => '3508120503850001', 'nama' => 'Drs. H. Ahmad Fauzi, M.Pd.',
                'jk' => 'L', 'agama' => 'Islam', 'ttl' => 'Banyuwangi, 12 Maret 1985',
                'nip_lengkap' => '19850312 201001 1 003', 'jabatan' => 'Kepala Madrasah',
                'unit' => 'Pimpinan', 'status_pegawai' => 'pns', 'status' => 'aktif',
                'hp' => '081234500001', 'email' => 'kepala@madrasah.sch.id', 'tmt' => '2010-01-01',
            ],
            [
                'nip' => '198702102011012004', 'nik' => '3508131002870002', 'nama' => 'Dra. Siti Nurhayati',
                'jk' => 'P', 'agama' => 'Islam', 'ttl' => 'Jember, 10 Februari 1987',
                'nip_lengkap' => '19870210 201101 2 004', 'jabatan' => 'Wakamad Kurikulum',
                'unit' => 'Kurikulum', 'status_pegawai' => 'pns', 'status' => 'aktif',
                'hp' => '081234500002', 'email' => 'kurikulum@madrasah.sch.id', 'tmt' => '2011-02-01',
            ],
            [
                'nip' => '199001152019031005', 'nik' => '3508141501900003', 'nama' => 'Bapak Umar Hakim, S.Pd.',
                'jk' => 'L', 'agama' => 'Islam', 'ttl' => 'Bondowoso, 15 Januari 1990',
                'nip_lengkap' => '19900115 201903 1 005', 'jabatan' => 'Guru Mata Pelajaran',
                'unit' => 'Guru', 'status_pegawai' => 'pppk', 'status' => 'aktif',
                'hp' => '081234500003', 'email' => 'guru.umar@madrasah.sch.id', 'tmt' => '2019-03-01',
            ],
            [
                'nip' => '—', 'nik' => '3508152107960004', 'nama' => 'Ratna Dewi, S.E.',
                'jk' => 'P', 'agama' => 'Islam', 'ttl' => 'Situbondo, 21 Juli 1996',
                'nip_lengkap' => '—', 'jabatan' => 'Bendahara',
                'unit' => 'Tata Usaha', 'status_pegawai' => 'honor', 'status' => 'aktif',
                'hp' => '081234500004', 'email' => 'bendahara@madrasah.sch.id', 'tmt' => '2020-07-01',
            ],
            [
                'nip' => '—', 'nik' => '3508160504990005', 'nama' => 'Sari Indah Puspitasari, A.Md.',
                'jk' => 'P', 'agama' => 'Islam', 'ttl' => 'Probolinggo, 5 April 1999',
                'nip_lengkap' => '—', 'jabatan' => 'Tata Usaha',
                'unit' => 'Tata Usaha', 'status_pegawai' => 'honor', 'status' => 'aktif',
                'hp' => '081234500005', 'email' => 'tu@madrasah.sch.id', 'tmt' => '2021-08-01',
            ],
            [
                'nip' => '—', 'nik' => '3508171205930006', 'nama' => 'Imam Syafii, S.Pd.',
                'jk' => 'L', 'agama' => 'Islam', 'ttl' => 'Lumajang, 12 Mei 1993',
                'nip_lengkap' => '—', 'jabatan' => 'Guru Mata Pelajaran',
                'unit' => 'Guru', 'status_pegawai' => 'honor', 'status' => 'aktif',
                'hp' => '081234500006', 'email' => 'guru.imam@madrasah.sch.id', 'tmt' => '2018-07-15',
            ],
            [
                'nip' => '—', 'nik' => '3508180107900007', 'nama' => 'Nurul Aini, S.Pd.',
                'jk' => 'P', 'agama' => 'Islam', 'ttl' => 'Pasuruan, 1 Juli 1990',
                'nip_lengkap' => '—', 'jabatan' => 'Guru BK',
                'unit' => 'Kesiswaan', 'status_pegawai' => 'pppk', 'status' => 'aktif',
                'hp' => '081234500007', 'email' => 'guru.bk@madrasah.sch.id', 'tmt' => '2022-09-01',
            ],
            [
                'nip' => '—', 'nik' => '3508193008980008', 'nama' => 'Hasan Basri, S.Kom.',
                'jk' => 'L', 'agama' => 'Islam', 'ttl' => 'Banyuwangi, 30 Agustus 1998',
                'nip_lengkap' => '—', 'jabatan' => 'Petugas Perpustakaan',
                'unit' => 'Perpustakaan', 'status_pegawai' => 'honor', 'status' => 'aktif',
                'hp' => '081234500008', 'email' => 'pustaka@madrasah.sch.id', 'tmt' => '2023-01-10',
            ],
        ];
    }
}
