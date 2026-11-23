<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreArticleRequest;
use App\Http\Requests\Api\V1\UpdateArticleRequest;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Models\Article;
use App\Services\Publikasi\ArticleService;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiResponse;
use App\Support\Api\ApiScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:20'],
        ]);

        if (isset($validated['status']) && ! $request->user()->tokenCan(ApiScope::CMS_WRITE)) {
            throw ApiException::forbidden(
                'Melihat berita non-publik membutuhkan scope cms:write.',
                ApiErrorCode::SCOPE_MISSING,
            );
        }

        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);

        $articles = Article::query()
            ->with('author:id,name')
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(! isset($validated['status']), fn ($query) => $query
                ->where('status', Article::PUBLISH)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now()))
            ->when($validated['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($validated['kategori'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->latest('published_at')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $articles,
            $articles->getCollection()->map(fn (Article $article) => ArticleResource::make($article)->resolve($request))->all(),
            ['filters' => $validated],
            ['Daftar default hanya memuat berita yang sudah dipublikasikan.'],
        );
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $this->authorize('create', Article::class);

        $article = $this->articles->create($request->validated(), $request->file('featured_image'), $request->user()->id);

        ApiAudit::log($request, 'api_berita_dibuat', $article, ['status' => $article->status]);

        return ApiResponse::success(
            ArticleResource::make($article->load('author'))->resolve($request),
            warnings: ['Berita dibuat sebagai draft. Publikasi dilakukan melalui transisi status di web.'],
            status: 201,
        );
    }

    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $this->authorize('update', $article);

        $validated = $request->validated();
        $publish = ($validated['status'] ?? null) === Article::PUBLISH;

        if (! $publish && ! in_array($article->status, [Article::DRAFT, Article::REVISI], true)) {
            throw ApiException::conflict(
                'Hanya berita draft/revisi yang dapat diubah melalui API.',
                ApiErrorCode::RECORD_ALREADY_PUBLISHED,
            );
        }

        if ($publish && ! $article->canTransitionTo(Article::PUBLISH)) {
            throw ApiException::business(
                'Berita hanya dapat dipublikasikan dari status disetujui atau dijadwalkan.',
            );
        }

        $this->articles->update($article, $validated, $request->file('featured_image'));

        if ($publish) {
            $publishedAt = $validated['published_at'] ?? null;
            $article->transitionTo(Article::PUBLISH, null, $request->user()->id, $publishedAt);

            ApiAudit::log($request, 'api_berita_dipublikasikan', $article, [
                'published_at' => $article->published_at?->toIso8601String(),
                'manual' => $publishedAt !== null,
            ]);
        } else {
            ApiAudit::log($request, 'api_berita_diubah', $article);
        }

        return ApiResponse::success(
            ArticleResource::make($article->fresh('author'))->resolve($request),
            warnings: $publish ? [] : ['Status berita tidak diubah oleh API.'],
        );
    }
}
