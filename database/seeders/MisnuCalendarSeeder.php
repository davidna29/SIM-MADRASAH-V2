<?php

namespace Database\Seeders;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Seeder;

class MisnuCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::where('is_active', true)->first();

        if (! $tahun) {
            return;
        }

        $adminId = User::where('username', 'admin')->value('id');

        AcademicCalendarPeriod::updateOrCreate(
            ['academic_year_id' => $tahun->id, 'semester' => 'ganjil'],
            [
                'start_date' => '2026-07-13',
                'end_date' => '2026-12-19',
                'effective_weeks' => 19,
                'effective_days' => 115,
                'note' => '27 pekan tersedia; 19 pekan efektif (KM 2026/2027).',
            ]
        );

        AcademicCalendarPeriod::updateOrCreate(
            ['academic_year_id' => $tahun->id, 'semester' => 'genap'],
            [
                'start_date' => '2027-01-04',
                'end_date' => '2027-06-19',
                'effective_weeks' => 17,
                'effective_days' => 100,
                'note' => '25 pekan tersedia; 17 pekan efektif (KM 2026/2027).',
            ]
        );

        $events = [
            ['Awal Masuk Tahun Ajaran 2026/2027', 'semester', '2026-07-13', null, false, 'Permulaan tahun ajaran.'],
            ['Pengenalan Lingkungan Madrasah (PLM)', 'kegiatan', '2026-07-13', '2026-07-18', false, 'Masa pengenalan bagi peserta didik baru.'],
            ['Hari Kemerdekaan Indonesia', 'nasional', '2026-08-17', null, true, 'Upacara bendera peringatan HUT RI.'],
            ['Maulid Nabi Muhammad saw.', 'keagamaan', '2026-08-25', null, true, 'Peringatan maulid Nabi.'],
            ['Asesmen Sumatif Akhir Semester Gasal', 'asesmen', '2026-11-23', '2026-12-05', false, 'Rentang ASAS gasal.'],
            ['Penyerahan Laporan Hasil Belajar Gasal', 'rapor', '2026-12-18', '2026-12-19', false, 'Pembagian rapor semester gasal.'],
            ['Hari Raya Natal', 'nasional', '2026-12-25', null, true, 'Libur nasional.'],
            ['Libur Semester Gasal', 'libur', '2026-12-21', '2027-01-02', true, 'Libur akhir semester gasal.'],

            ['Tahun Baru Masehi', 'nasional', '2027-01-01', null, true, 'Libur nasional.'],
            ['Hari Amal Bakti Kementerian Agama', 'nasional', '2027-01-03', null, true, 'HAB Kemenag RI.'],
            ['Awal Masuk Semester Genap', 'semester', '2027-01-04', null, false, 'Permulaan semester genap.'],
            ['Isra Mi’raj Nabi Muhammad saw.', 'keagamaan', '2027-01-05', null, true, 'Peringatan Isra Mi’raj.'],
            ['Tahun Baru Imlek', 'nasional', '2027-02-06', null, true, 'Libur nasional.'],
            ['Libur Seputar Idulfitri 1448 H', 'libur', '2027-03-06', '2027-03-13', true, 'Menyesuaikan ketetapan pemerintah.'],
            ['Hari Raya Nyepi', 'nasional', '2027-03-09', null, true, 'Libur nasional.'],
            ['Idulfitri 1448 H', 'keagamaan', '2027-03-10', '2027-03-11', true, 'Salat Id dan silaturahmi.'],
            ['Wafat Isa Almasih', 'nasional', '2027-03-26', null, true, 'Libur nasional.'],
            ['Ujian Madrasah Kelas VI', 'asesmen', '2027-03-29', '2027-05-15', false, 'Rentang waktu ujian madrasah.'],
            ['Hari Buruh Internasional', 'nasional', '2027-05-01', null, true, 'Libur nasional.'],
            ['Kenaikan Isa Almasih', 'nasional', '2027-05-06', null, true, 'Libur nasional.'],
            ['Idul Adha 1448 H', 'keagamaan', '2027-05-17', null, true, 'Libur nasional.'],
            ['Hari Raya Waisak', 'nasional', '2027-05-20', null, true, 'Libur nasional.'],
            ['Asesmen Sumatif Akhir Semester Genap', 'asesmen', '2027-05-24', '2027-06-05', false, 'Perkiraan ASAS genap.'],
            ['Hari Lahir Pancasila', 'nasional', '2027-06-01', null, true, 'Libur nasional.'],
            ['Penyerahan Laporan Hasil Belajar Genap', 'rapor', '2027-06-18', '2027-06-19', false, 'Pembagian rapor semester genap.'],
            ['Libur Akhir Tahun Ajaran', 'libur', '2027-06-21', '2027-07-10', true, 'Libur akhir tahun ajaran 2026/2027.'],
        ];

        foreach ($events as [$title, $category, $start, $end, $isLibur, $description]) {
            // PLM, asesmen, dan pembagian rapor tetap berlangsung tetapi bukan
            // hari efektif pembelajaran.
            $isEfektif = $category !== 'rapor'
                && ! str_contains($title, 'PLM')
                && ! str_contains($title, 'Asesmen Sumatif');

            AcademicCalendarEvent::updateOrCreate(
                [
                    'academic_year_id' => $tahun->id,
                    'title' => $title,
                    'start_date' => $start,
                ],
                [
                    'category' => $category,
                    'end_date' => $end,
                    'is_libur' => $isLibur,
                    'is_efektif' => $isEfektif,
                    'description' => $description,
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
