<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrestasiTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'NIS', 'Jenis', 'Nama Kegiatan', 'Tingkat', 'Penyelenggara',
            'Tanggal', 'Peringkat', 'Pembimbing', 'Status Publikasi',
        ];
    }

    public function array(): array
    {
        return [
            ['240101', 'nonakademik', 'Lomba Pidato Bahasa Arab', 'kabupaten', 'Kemenag Kabupaten', '2026-07-15', 'Juara 1', 'Bapak Imam Syafii', 'publik'],
            ['240102', 'akademik', 'Olimpiade Matematika', 'provinsi', 'Puspresnas', '2026-08-01', 'Finalis', 'Bapak Umar Hakim', 'internal'],
        ];
    }
}
