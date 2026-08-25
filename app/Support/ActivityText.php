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
            default => ucfirst(str_replace('_', ' ', $description)),
        };
    }
}
