<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Support\DashboardData;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahun = AcademicYear::active();

        return view('pages.dashboard', [
            'roleLabel' => match (auth()->user()->role) {
                'kepala_madrasah' => 'Kepala Madrasah',
                'wakamad_kurikulum' => 'Wakamad Kurikulum',
                'wakamad_kesiswaan' => 'Wakamad Kesiswaan',
                'bendahara' => 'Bendahara',
                'tata_usaha' => 'Tata Usaha',
                default => 'Super Admin',
            },
            'breadcrumb' => [['label' => 'Fondasi & Pengaturan'], ['label' => 'Dashboard']],
            'tahun' => $tahun,
            'kpis' => DashboardData::kpis($tahun),
            'perluTindakan' => collect(DashboardData::perluTindakan($tahun)),
            'kehadiranRombel' => DashboardData::kehadiranRombel($tahun),
            'tagihan' => DashboardData::tagihanTerbaru($tahun),
            'aktivitas' => DashboardData::aktivitas(),
        ]);
    }
}
