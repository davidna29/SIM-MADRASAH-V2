<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EmployeeResource;
use App\Models\Employee;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'integer', 'exists:organizational_units,id'],
            'jabatan' => ['nullable', 'integer', 'exists:positions,id'],
            'status' => ['nullable', 'in:aktif,cuti,nonaktif'],
        ]);

        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);

        $employees = Employee::query()
            ->with(['person:id,name', 'position:id,name', 'organizationalUnit:id,name'])
            ->when($validated['q'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->whereHas('person', fn ($person) => $person->where('name', 'like', "%{$search}%"))
                    ->orWhere('nip', 'like', "%{$search}%");
            }))
            ->when($validated['unit'] ?? null, fn ($query, $id) => $query->where('organizational_unit_id', $id))
            ->when($validated['jabatan'] ?? null, fn ($query, $id) => $query->where('position_id', $id))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $employees,
            $employees->getCollection()->map(fn (Employee $employee) => EmployeeResource::make($employee)->resolve($request))->all(),
            ['filters' => $validated],
            ['NIK, tanggal lahir, alamat, telepon, dan email tidak ditampilkan oleh API.'],
        );
    }
}
