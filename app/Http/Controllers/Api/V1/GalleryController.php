<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAlbumRequest;
use App\Http\Requests\Api\V1\StoreGalleryItemRequest;
use App\Http\Requests\Api\V1\UpdateAlbumRequest;
use App\Http\Resources\Api\V1\MediaAlbumResource;
use App\Models\MediaAlbum;
use App\Services\Publikasi\GalleryService;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiResponse;
use App\Support\Api\ApiScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $galleries) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:publik,privat'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        if (isset($validated['status']) && ! $request->user()->tokenCan(ApiScope::CMS_WRITE)) {
            throw ApiException::forbidden(
                'Melihat album non-publik membutuhkan scope cms:write.',
                ApiErrorCode::SCOPE_MISSING,
            );
        }

        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);
        $approvedItems = fn ($query) => $query
            ->whereNotNull('publication_approved_at')
            ->where(fn ($items) => $items
                ->where('tipe', 'video')
                ->orWhere(fn ($photos) => $photos->where('tipe', 'foto')->where('file_path', 'not like', '%demo-%')));

        $albums = MediaAlbum::query()
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(! isset($validated['status']), fn ($query) => $query->where('status', 'publik'))
            ->when(! isset($validated['status']), fn ($query) => $query->whereDoesntHave('items', fn ($items) => $items->where('file_path', 'like', '%demo-%')))
            ->when($validated['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->withCount(['items as items_count' => $approvedItems])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $albums,
            $albums->getCollection()->map(fn (MediaAlbum $album) => MediaAlbumResource::make($album)->resolve($request))->all(),
            ['filters' => $validated],
            ['Daftar default hanya memuat album publik dan media yang sudah disetujui.'],
        );
    }

    public function store(StoreAlbumRequest $request): JsonResponse
    {
        $this->authorize('create', MediaAlbum::class);

        $validated = [...$request->validated(), 'status' => 'privat'];
        $album = $this->galleries->create($validated, $request->file('cover_image'), $request->user()->id);

        ApiAudit::log($request, 'api_galeri_dibuat', $album, ['status' => $album->status]);

        return ApiResponse::success(
            MediaAlbumResource::make($album)->resolve($request),
            warnings: ['Album dibuat privat. Publikasi dan unggah media dilakukan melalui web.'],
            status: 201,
        );
    }

    public function storeItem(StoreGalleryItemRequest $request, MediaAlbum $album): JsonResponse
    {
        $this->authorize('update', $album);

        if ($album->status !== 'privat') {
            throw ApiException::conflict(
                'Item hanya dapat ditambahkan ke album privat melalui API.',
                ApiErrorCode::RECORD_ALREADY_PUBLISHED,
            );
        }

        $item = $this->galleries->addItem($album, $request->validated(), $request->file('file'));

        ApiAudit::log($request, 'api_galeri_item_ditambah', $item, [
            'album_id' => $album->id,
            'tipe' => $item->tipe,
        ]);

        return ApiResponse::success(
            [
                'id' => $item->id,
                'album_id' => $item->album_id,
                'tipe' => $item->tipe,
                'title' => $item->title,
                'deskripsi' => $item->description,
                'file_url' => $item->file_path ? Storage::disk('public')->url($item->file_path) : null,
                'video_url' => $item->video_url,
                'publication_approved_at' => null,
                'sort_order' => $item->sort_order,
            ],
            warnings: ['Item tersimpan tanpa persetujuan publikasi. Setujui melalui CMS sebelum tampil di website.'],
            status: 201,
        );
    }

    public function update(UpdateAlbumRequest $request, MediaAlbum $album): JsonResponse
    {
        $this->authorize('update', $album);

        if ($album->status !== 'privat') {
            throw ApiException::conflict(
                'Hanya album privat yang dapat diubah melalui API.',
                ApiErrorCode::RECORD_ALREADY_PUBLISHED,
            );
        }

        $this->galleries->update($album, [...$request->validated(), 'status' => 'privat'], $request->file('cover_image'));

        ApiAudit::log($request, 'api_galeri_diubah', $album);

        return ApiResponse::success(
            MediaAlbumResource::make($album->fresh())->resolve($request),
            warnings: ['Status album tetap privat; publikasi dilakukan melalui web.'],
        );
    }
}
