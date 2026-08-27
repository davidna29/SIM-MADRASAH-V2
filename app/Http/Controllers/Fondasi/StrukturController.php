<?php

namespace App\Http\Controllers\Fondasi;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\View\View;

class StrukturController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Employee::class);

        $units = OrganizationalUnit::with(['employees' => function ($q) {
            $q->with(['person', 'position'])->where('status', 'aktif')->orderBy('id');
        }])->orderBy('code')->get();

        return view('pages.fondasi.struktur', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Sistem', 'href' => route('dashboard')],
                ['label' => 'Struktur Organisasi'],
            ],
            'units' => $units,
        ]);
    }
}
