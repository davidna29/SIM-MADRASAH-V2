<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PpiExamArchiveTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'nisn' => '0012345678',
                'nama' => 'Contoh Siswa',
                'rata_p1' => 85,
                'rata_p2' => 88,
                'rata_p3' => 90,
                'nilai_hafalan' => 92,
                'nilai_akhir' => 88.75,
                'predikat' => 'A',
                'status_lulus' => 'Lulus',
                'rank' => 1,
                'rombel' => 'VI-A',
            ],
        ];
    }

    public function headings(): array
    {
        return ['NISN', 'Nama', 'Rata P1', 'Rata P2', 'Rata P3', 'Nilai Hafalan', 'Nilai Akhir', 'Predikat', 'Status Lulus', 'Rank', 'Rombel'];
    }
}
