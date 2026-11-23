<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAgendaRequest;
use App\Http\Requests\Api\V1\UpdateAgendaRequest;
use App\Http\Resources\Api\V1\AgendaResource;
use App\Models\Agenda;
use App\Services\Publikasi\AgendaService;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiResponse;
use App\Support\Api\ApiScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function __construct(private readonly AgendaService $agendas) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'jenis' => ['nullable', 'in:agenda,pengumuman'],
            'status' => ['nullable', 'in:aktif,arsip'],
            'target' => ['nullable', 'in:publik,internal'],
        ]);

        $needsCms = isset($validated['status']) || ($validated['target'] ?? null) === 'internal';

        if ($needsCms && ! $request->user()->tokenCan(ApiScope::CMS_WRITE)) {
            throw ApiException::forbidden(
                'Melihat agenda non-publik membutuhkan scope cms:write.',
                ApiErrorCode::SCOPE_MISSING,
            );
        }

        $today = now()->toDateString();
        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);

        $agenda = Agenda::query()
            ->with('creator:id,name')
            ->when($validated['jenis'] ?? null, fn ($query, $type) => $query->where('jenis', $type))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(! isset($validated['status']), fn ($query) => $query->where('status', 'aktif'))
            ->when($validated['target'] ?? null, fn ($query, $target) => $query->where('target', $target))
            ->when(! isset($validated['target']), fn ($query) => $query->where('target', 'publik'))
            ->when(! $needsCms, fn ($query) => $query
                ->whereDate('tampil_mulai', '<=', $today)
                ->where(fn ($query) => $query->whereNull('tampil_selesai')->orWhereDate('tampil_selesai', '>=', $today)))
            ->orderBy('tanggal')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $agenda,
            $agenda->getCollection()->map(fn (Agenda $item) => AgendaResource::make($item)->resolve($request))->all(),
            ['filters' => $validated],
            ['Daftar default hanya memuat agenda publik yang sedang tampil.'],
        );
    }

    public function store(StoreAgendaRequest $request): JsonResponse
    {
        $this->authorize('create', Agenda::class);

        $data = [...$request->validated(), 'status' => 'arsip'];
        $agenda = $this->agendas->create($data, $request->user()->id);

        ApiAudit::log($request, 'api_agenda_dibuat', $agenda, ['status' => $agenda->status]);

        return ApiResponse::success(
            AgendaResource::make($agenda->load('creator'))->resolve($request),
            warnings: ['Agenda dibuat sebagai arsip. Aktifkan publikasinya melalui web.'],
            status: 201,
        );
    }

    public function update(UpdateAgendaRequest $request, Agenda $agenda): JsonResponse
    {
        $this->authorize('update', $agenda);

        if ($agenda->status !== 'arsip') {
            throw ApiException::conflict(
                'Hanya agenda berstatus arsip yang dapat diubah melalui API.',
                ApiErrorCode::RECORD_ALREADY_PUBLISHED,
            );
        }

        $this->agendas->update($agenda, [...$request->validated(), 'status' => 'arsip']);

        ApiAudit::log($request, 'api_agenda_diubah', $agenda);

        return ApiResponse::success(
            AgendaResource::make($agenda->fresh('creator'))->resolve($request),
            warnings: ['Status agenda tetap arsip; publikasi dilakukan melalui web.'],
        );
    }
}
