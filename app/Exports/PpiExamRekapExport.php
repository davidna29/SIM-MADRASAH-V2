<?php

namespace App\Exports;

use App\Models\PpiExamPeriod;
use App\Services\PpiExamService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PpiExamRekapExport implements FromCollection, WithHeadings, WithStyles
{
    protected array $data;

    protected PpiExamPeriod $periode;

    protected PpiExamService $service;

    protected array $headers = [];

    public function __construct(array $data, PpiExamPeriod $periode, PpiExamService $service)
    {
        $this->data = $data;
        $this->periode = $periode;
        $this->service = $service;

        $this->headers = $this->buildHeaders();
    }

    public function collection(): Collection
    {
        $service = $this->service;
        $rows = [];

        foreach ($this->data['participants'] as $p) {
            $row = [
                $p->no_urut,
                $p->student ? $service->nisnOf($p->student) : '—',
                $p->student?->name ?? '—',
                $p->room?->nama ?? '—',
            ];

            foreach ($this->data['categories'] as $category) {
                foreach ($category->aspects as $aspect) {
                    $row[] = $this->data['scores'][$p->id][$aspect->id]->nilai ?? '';
                }
            }

            foreach ($this->data['hafalanMateri'] as $materi) {
                $row[] = $this->data['hafalanScores'][$p->id][$materi->id]->nilai ?? '';
            }

            $row = array_merge($row, [
                $p->jumlah_p1 ?? '',
                $service::fmt($p->rata_p1),
                $p->jumlah_p2 ?? '',
                $service::fmt($p->rata_p2),
                $p->jumlah_p3 ?? '',
                $service::fmt($p->rata_p3),
                $service::fmt($p->rata_hafalan),
                $p->jumlah_ujian_lisan ?? '',
                $service::fmt($p->rata_ujian_lisan),
                $p->predicateScale?->predikat ?? '—',
                $p->predicateScale?->deskripsi ?? '—',
                $p->status_lulus === null ? '—' : ($p->status_lulus ? 'Lulus' : 'Tidak Lulus'),
                $p->student?->gender ?? '—',
                $p->student ? $service->fatherName($p->student) : '—',
                $p->group?->nama ?? '—',
                $p->rank_total ?? '',
                $p->rank_lokal ?? '',
            ]);

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return $this->headers;
    }

    protected function buildHeaders(): array
    {
        $headers = ['No Urut', 'NISN', 'Nama Siswa', 'Ruang'];

        foreach ($this->data['categories'] as $category) {
            foreach ($category->aspects as $aspect) {
                $headers[] = trim($category->kode.'.'.$aspect->kode, '.') ?: $aspect->nama;
            }
        }

        foreach ($this->data['hafalanMateri'] as $materi) {
            $headers[] = 'Hafalan: '.$materi->nama;
        }

        return array_merge($headers, [
            'Jumlah P1', 'Rata P1', 'Jumlah P2', 'Rata P2', 'Jumlah P3', 'Rata P3',
            'Rata Hafalan', 'Jumlah', 'Rata', 'Predikat', 'Deskripsi',
            'Status Lulus', 'Gender', 'Nama Ayah', 'Grup Setoran', 'Rank Total', 'Rank Lokal',
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:Z1' => [
                'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FFE0E0E0']],
            ],
        ];
    }
}
