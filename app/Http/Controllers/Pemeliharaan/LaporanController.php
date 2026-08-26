<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Employee;
use App\Support\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Employee::class);

        return view('pages.pemeliharaan.laporan.index', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan'],
            ],
        ]);
    }

    public function akademik(): View
    {
        $this->authorize('viewAny', Employee::class);

        $tahun = AcademicYear::active();
        $data = LaporanService::rekapAkademik($tahun);

        return view('pages.pemeliharaan.laporan.akademik', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan', 'href' => route('laporan.index')],
                ['label' => 'Rekap Akademik'],
            ],
            'data' => $data,
            'tahun' => $tahun,
        ]);
    }

    public function kehadiran(): View
    {
        $this->authorize('viewAny', Employee::class);

        $tahun = AcademicYear::active();
        $data = LaporanService::rekapKehadiran($tahun);

        return view('pages.pemeliharaan.laporan.kehadiran', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan', 'href' => route('laporan.index')],
                ['label' => 'Rekap Kehadiran'],
            ],
            'data' => $data,
            'tahun' => $tahun,
        ]);
    }

    public function keuangan(): View
    {
        $this->authorize('viewAny', Employee::class);

        $tahun = AcademicYear::active();
        $data = LaporanService::rekapKeuangan($tahun);

        return view('pages.pemeliharaan.laporan.keuangan', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan', 'href' => route('laporan.index')],
                ['label' => 'Rekap Keuangan'],
            ],
            'data' => $data,
            'tahun' => $tahun,
        ]);
    }

    public function kesiswaan(): View
    {
        $this->authorize('viewAny', Employee::class);

        $tahun = AcademicYear::active();
        $data = LaporanService::rekapKesiswaan($tahun);

        return view('pages.pemeliharaan.laporan.kesiswaan', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan', 'href' => route('laporan.index')],
                ['label' => 'Rekap Kesiswaan'],
            ],
            'data' => $data,
            'tahun' => $tahun,
        ]);
    }

    public function tenaga(): View
    {
        $this->authorize('viewAny', Employee::class);

        $data = LaporanService::rekapTenaga();

        return view('pages.pemeliharaan.laporan.tenaga', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan', 'href' => route('laporan.index')],
                ['label' => 'Rekap Tenaga'],
            ],
            'data' => $data,
        ]);
    }

    public function perpustakaan(): View
    {
        $this->authorize('viewAny', Employee::class);

        $data = LaporanService::rekapPerpustakaan();

        return view('pages.pemeliharaan.laporan.perpustakaan', [
            'roleLabel' => 'Pemeliharaan',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Pusat Laporan', 'href' => route('laporan.index')],
                ['label' => 'Rekap Perpustakaan'],
            ],
            'data' => $data,
        ]);
    }

    public function exportPdf(string $jenis)
    {
        $this->authorize('viewAny', Employee::class);

        $tahun = AcademicYear::active();
        $title = match ($jenis) {
            'akademik' => 'Rekap Akademik',
            'kehadiran' => 'Rekap Kehadiran',
            'keuangan' => 'Rekap Keuangan',
            'kesiswaan' => 'Rekap Kesiswaan',
            'tenaga' => 'Rekap Tenaga',
            'perpustakaan' => 'Rekap Perpustakaan',
            default => abort(404),
        };

        $data = match ($jenis) {
            'akademik' => LaporanService::rekapAkademik($tahun),
            'kehadiran' => LaporanService::rekapKehadiran($tahun),
            'keuangan' => LaporanService::rekapKeuangan($tahun),
            'kesiswaan' => LaporanService::rekapKesiswaan($tahun),
            'tenaga' => LaporanService::rekapTenaga(),
            'perpustakaan' => LaporanService::rekapPerpustakaan(),
        };

        $pdf = Pdf::loadView('pages.pemeliharaan.laporan.print', [
            'title' => $title,
            'jenis' => $jenis,
            'data' => $data,
            'tahun' => $tahun,
        ])->setPaper('a4', 'landscape');

        $filename = "laporan-{$jenis}-".($tahun?->name ?? 'semua').'.pdf';

        return $pdf->download($filename);
    }

    public function exportCsv(string $jenis)
    {
        $this->authorize('viewAny', Employee::class);

        $tahun = AcademicYear::active();

        $data = match ($jenis) {
            'akademik' => LaporanService::rekapAkademik($tahun),
            'kehadiran' => LaporanService::rekapKehadiran($tahun),
            'keuangan' => LaporanService::rekapKeuangan($tahun),
            'kesiswaan' => LaporanService::rekapKesiswaan($tahun),
            'tenaga' => LaporanService::rekapTenaga(),
            'perpustakaan' => LaporanService::rekapPerpustakaan(),
            default => abort(404),
        };

        $filename = "laporan-{$jenis}-".($tahun?->name ?? 'semua').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if (isset($data['rows'])) {
                // Header
                if ($data['rows']->isNotEmpty()) {
                    fputcsv($file, array_keys($data['rows']->first()));
                }
                // Data
                foreach ($data['rows'] as $row) {
                    fputcsv($file, array_values($row));
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
