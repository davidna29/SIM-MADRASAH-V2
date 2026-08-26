<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembiasaanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected array $rows;

    protected array $pairs;

    protected array $footers;

    protected Student $siswa;

    protected string $title;

    protected array $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];

    public function __construct(array $data, Student $siswa)
    {
        $this->rows = $data['rows']->toArray();
        $this->pairs = $data['pairs'];
        $this->footers = $data['footers'];
        $this->siswa = $siswa;
        $this->title = $data['title'] ?? 'Nilai';
    }

    public function collection(): Collection
    {
        $rows = collect($this->rows);

        // Tambah baris footer
        $jumlahRow = ['' => '', 'Jumlah' => ''];
        $rataRow = ['' => '', 'Rata-rata' => ''];
        $kategoriRow = ['' => '', 'Kategori' => ''];
        foreach ($this->pairs as $pair) {
            $key = "{$pair[0]}-{$pair[1]}";
            $col = $this->grade[$pair[0]].'.'.$pair[1];
            $jumlahRow[$col] = $this->footers[$key]['jumlah'] ?? '';
            $rataRow[$col] = $this->footers[$key]['rata_rata'] ?? '';
            $kategoriRow[$col] = $this->footers[$key]['kategori'] ?? '';
        }

        $footerRows = collect([$jumlahRow, $rataRow, $kategoriRow]);

        return $rows->map(fn ($row) => $this->mapRow($row))->concat($footerRows);
    }

    public function headings(): array
    {
        $headers = ['No', 'Materi'];
        foreach ($this->pairs as $pair) {
            $headers[] = $this->grade[$pair[0]].'.'.$pair[1];
        }

        return $headers;
    }

    public function map($row): array
    {
        return $row; // handled by mapRow
    }

    protected function mapRow(array $row): array
    {
        $cells = ['No' => $row['materi']['no_urut'], 'Materi' => $row['materi']['nama_materi']];
        foreach ($row['cells'] as $cell) {
            $col = $this->grade[$cell['kelas']].'.'.$cell['semester'];
            $cells[$col] = $cell['nilai'] ?? '';
        }

        return $cells;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = count($this->pairs) + 2; // No + Materi + pairs
        $lastRow = count($this->rows) + 4; // header + rows + 3 footer rows

        return [
            1 => ['font' => ['bold' => true]],
            $lastRow - 2 => ['font' => ['bold' => true]],
            $lastRow - 1 => ['font' => ['bold' => true]],
            $lastRow => ['font' => ['bold' => true]],
            'A1:'.$this->colLetter($lastCol).'1' => [
                'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FFE0E0E0']],
            ],
        ];
    }

    protected function colLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $col--;
            $letter = chr(65 + ($col % 26)).$letter;
            $col = intdiv($col, 26);
        }

        return $letter;
    }
}
