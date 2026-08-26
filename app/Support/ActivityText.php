<?php

namespace App\Support;

class ActivityText
{
    public static function readable(string $description): string
    {
        return match ($description) {
            'kehadiran_diinput' => 'mencatat kehadiran siswa',
            'jurnal_diisi' => 'mengisi jurnal mengajar',
            'jurnal_diubah' => 'mengubah jurnal mengajar',
            'jurnal_dihapus' => 'menghapus jurnal mengajar',
            'jadwal_disusun' => 'menyusun jadwal pelajaran',
            'jadwal_generate_blank' => 'membuat kerangka kosong jadwal',
            'jadwal_generate_copy' => 'menyalin jadwal tahun sebelumnya',
            'rapor_diterbitkan' => 'menerbitkan rapor',
            'spp_dibayar' => 'mencatat pembayaran SPP',
            'spp_diubah' => 'mengubah pembayaran SPP',
            'spp_nominal_diatur' => 'mengatur nominal SPP',
            'spp_override_diatur' => 'mengatur keringanan SPP',
            'berita_dibuat' => 'membuat berita',
            'berita_diubah' => 'mengubah berita',
            'berita_dihapus' => 'menghapus berita',
            'agenda_dibuat' => 'membuat agenda/pengumuman',
            'agenda_diubah' => 'mengubah agenda/pengumuman',
            'agenda_dihapus' => 'menghapus agenda/pengumuman',
            'prestasi_dicatat' => 'mencatat prestasi siswa',
            'prestasi_diubah' => 'mengubah prestasi siswa',
            'prestasi_dihapus' => 'menghapus prestasi siswa',
            'prestasi_import' => 'mengimport prestasi dari Excel',
            'pelanggaran_dicatat' => 'mencatat pelanggaran siswa',
            'pelanggaran_diubah' => 'mengubah pelanggaran siswa',
            'pelanggaran_dihapus' => 'menghapus pelanggaran siswa',
            'barang_ditambah' => 'menambahkan barang inventaris',
            'barang_diubah' => 'mengubah barang inventaris',
            'barang_dihapus' => 'menghapus barang inventaris',
            'barang_mutasi_diajukan' => 'mengajukan mutasi barang inventaris',
            'barang_mutasi_disetujui' => 'menyetujui mutasi barang inventaris',
            'barang_mutasi_ditolak' => 'menolak mutasi barang inventaris',
            'barang_mutasi_dibatalkan' => 'membatalkan mutasi barang inventaris',
            'barang_dipelihara' => 'mencatat pemeliharaan barang inventaris',
            'barang_perawatan_selesai' => 'menandai pemeliharaan barang selesai',
            'barang_perawatan_dihapus' => 'menghapus catatan pemeliharaan barang',
            'kategori_barang_dibuat' => 'membuat kategori barang inventaris',
            'kategori_barang_diubah' => 'mengubah kategori barang inventaris',
            'kategori_barang_dihapus' => 'menghapus kategori barang inventaris',
            'buku_ditambah' => 'menambahkan buku ke katalog perpustakaan',
            'buku_diubah' => 'mengubah data buku perpustakaan',
            'buku_dihapus' => 'menghapus buku dari katalog perpustakaan',
            'buku_dipinjam' => 'mencatat peminjaman buku perpustakaan',
            'buku_dikembalikan' => 'mencatat pengembalian buku perpustakaan',
            'anggota_perpustakaan_ditambah' => 'menambahkan anggota perpustakaan',
            'anggota_perpustakaan_dihapus' => 'menghapus anggota perpustakaan',
            'kategori_perpustakaan_dibuat' => 'membuat kategori perpustakaan',
            'kategori_perpustakaan_diubah' => 'mengubah kategori perpustakaan',
            'kategori_perpustakaan_dihapus' => 'menghapus kategori perpustakaan',
            default => ucfirst(str_replace('_', ' ', $description)),
        };
    }
}
