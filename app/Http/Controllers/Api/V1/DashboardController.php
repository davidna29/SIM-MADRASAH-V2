<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Support\Api\ApiAcademicYear;
use App\Support\Api\ApiResponse;
use App\Support\DashboardData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $allowed = ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan', 'bendahara', 'tata_usaha'];

        if (! in_array($request->user()->role, $allowed, true)) {
            throw ApiException::forbidden('Role Anda tidak memiliki akses ke ringkasan dashboard.');
        }

        $year = ApiAcademicYear::active();

        return ApiResponse::success([
            'academic_year' => [
                'id' => $year->id,
                'name' => $year->name,
                'semester' => $year->semester,
            ],
            'kpis' => DashboardData::kpis($year),
            'perlu_tindakan' => DashboardData::perluTindakan($year),
            'kehadiran_rombel' => collect(DashboardData::kehadiranRombel($year))->map(fn ($row) => [
                'class_group' => [
                    'id' => $row['class']->id,
                    'name' => $row['class']->name,
                ],
                'hadir' => $row['hadir'],
                'total' => $row['total'],
                'reviewed' => $row['reviewed'],
            ]),
            'tagihan_terbaru' => DashboardData::tagihanTerbaru($year),
            'aktivitas' => DashboardData::aktivitas(),
        ], warnings: []);
    }
}
