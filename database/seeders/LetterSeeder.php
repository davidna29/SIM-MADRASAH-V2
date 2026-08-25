<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Letter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LetterSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();
        $admin = User::where('username', 'admin')->first();
        $tataUsaha = User::where('username', 'tata_usaha')->first() ?? $admin;

        if (! $admin) {
            return;
        }

        // Surat masuk
        $suratMasuk = [
            [
                'type' => 'masuk',
                'number' => '001/SM/08/2026',
                'date' => Carbon::now()->subDays(5),
                'from_to' => 'Kantor Kementerian Agama Kabupaten',
                'subject' => 'Edaran Pelaksanaan Ujian Akhir Semester',
                'description' => 'Edaran tentang jadwal dan teknis pelaksanaan UAS semester ganjil tahun ajaran 2026/2027.',
                'status' => 'diterima',
                'priority' => 'penting',
                'category' => 'Edaran',
                'disposition_to' => 'Wakamad Kurikulum',
                'disposition_note' => 'Mohon ditindaklanjuti untuk penyebaran ke guru mapel.',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'masuk',
                'number' => '002/SM/08/2026',
                'date' => Carbon::now()->subDays(3),
                'from_to' => 'Dinas Pendidikan Provinsi',
                'subject' => 'Undangan Rapat Koordinasi Kepala Madrasah',
                'description' => 'Undangan rapat koordinasi untuk membahas programsemester depan.',
                'status' => 'diproses',
                'priority' => 'biasa',
                'category' => 'Undangan',
                'disposition_to' => 'Kepala Madrasah',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'masuk',
                'number' => '003/SM/08/2026',
                'date' => Carbon::now()->subDays(1),
                'from_to' => 'Yayasan Pendidikan Madrasah',
                'subject' => 'Pemberitahuan Libur Nasional',
                'description' => 'Pemberitahuan hari libur nasional dan cuti bersama.',
                'status' => 'diterima',
                'priority' => 'biasa',
                'category' => 'Pemberitahuan',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'masuk',
                'date' => Carbon::now(),
                'from_to' => 'PT. Maju Bersama',
                'subject' => 'Penawaran Kerja Sama Program Beasiswa',
                'description' => 'Penawaran program beasiswa untuk siswa berprestasi.',
                'status' => 'diterima',
                'priority' => 'penting',
                'category' => 'Lainnya',
                'recorded_by' => $admin->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'masuk',
                'number' => '005/SM/07/2026',
                'date' => Carbon::now()->subWeek(),
                'from_to' => 'Kantor Kementerian Agama Kabupaten',
                'subject' => 'Surat Tugas Pelatihan Guru',
                'description' => 'Surat tugas untuk mengikuti pelatihan metodologi pembelajaran.',
                'status' => 'selesai',
                'priority' => 'biasa',
                'category' => 'Surat Tugas',
                'disposition_to' => 'Wakamad Kurikulum',
                'disposition_note' => 'Daftarkan 3 guru untuk pelatihan.',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
        ];

        foreach ($suratMasuk as $surat) {
            Letter::create($surat);
        }

        // Surat keluar
        $suratKeluar = [
            [
                'type' => 'keluar',
                'number' => Letter::generateNumber(),
                'date' => Carbon::now()->subDays(4),
                'from_to' => 'Kantor Kementerian Agama Kabupaten',
                'subject' => 'Laporan Pelaksanaan Ujian Akhir Semester',
                'description' => 'Laporan hasil pelaksanaan UAS semester ganjil.',
                'status' => 'selesai',
                'priority' => 'penting',
                'category' => 'Laporan',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'keluar',
                'number' => Letter::generateNumber(),
                'date' => Carbon::now()->subDays(2),
                'from_to' => 'Dinas Pendidikan Provinsi',
                'subject' => 'Surat Undangan Rapat Koordinasi',
                'description' => 'Undangan rapat koordinasi kepala madrasah se-kabupaten.',
                'status' => 'diproses',
                'priority' => 'biasa',
                'category' => 'Undangan',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'keluar',
                'number' => Letter::generateNumber(),
                'date' => Carbon::now(),
                'from_to' => 'Kepala Dinas Pendidikan',
                'subject' => 'Permohonan Izin Kegiatan Outbound',
                'description' => 'Permohonan izin untuk melaksanakan kegiatan outbound siswa.',
                'status' => 'diterima',
                'priority' => 'segera',
                'category' => 'Permohonan',
                'recorded_by' => $admin->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'type' => 'keluar',
                'number' => Letter::generateNumber(),
                'date' => Carbon::now()->subDays(6),
                'from_to' => 'Orang Tua Siswa',
                'subject' => 'Surat Keterangan Aktif Siswa',
                'description' => 'Surat keterangan untuk keperluan administrasi.',
                'status' => 'arsip',
                'priority' => 'biasa',
                'category' => 'Surat Keterangan',
                'recorded_by' => $tataUsaha->id,
                'academic_year_id' => $tahun->id,
            ],
        ];

        foreach ($suratKeluar as $surat) {
            Letter::create($surat);
        }
    }
}
