<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class OrganisasiController extends Controller
{
    public function struktur(): View
    {
        return view('pages.publik.struktur', [
            'units' => $this->publicEmployees()->get()->groupBy(fn (Employee $employee) => $employee->organizationalUnit?->name ?? 'Lainnya'),
        ]);
    }

    public function guru(): View
    {
        return view('pages.publik.guru', [
            'employees' => $this->publicEmployees()->get(),
        ]);
    }

    private function publicEmployees(): Builder
    {
        return Employee::query()
            ->select(['id', 'person_id', 'organizational_unit_id', 'position_id', 'photo_path'])
            ->where('status', 'aktif')
            ->where('show_on_website', true)
            ->with([
                'person:id,name',
                'position:id,code,name',
                'organizationalUnit:id,code,name',
            ])
            ->orderBy('organizational_unit_id')
            ->orderBy('position_id');
    }
}
