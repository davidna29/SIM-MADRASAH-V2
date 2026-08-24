<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    protected array $editorRoles = ['editor_berita', 'wakamad_humas', 'kepala_madrasah', 'super_admin'];

    public function index(): View
    {
        $this->authorize('viewAny', Article::class);

        $articles = Article::with('author')
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->when(request('q'), fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('body', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.cms.berita.index', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda'],
                ['label' => 'Berita'],
            ],
            'articles' => $articles,
            'statusLabels' => Article::statusLabels(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('pages.cms.berita.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.berita.index')],
                ['label' => 'Tulis Berita'],
            ],
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate($this->rules());
        $slug = $this->uniqueSlug($validated['slug'] ?? Str::slug($validated['title']));

        $article = Article::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
            'body' => $validated['body'],
            'category' => $validated['category'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'featured_image' => $request->hasFile('featured_image') ? $request->file('featured_image')->store('berita', 'public') : null,
            'status' => Article::DRAFT,
            'author_id' => auth()->id(),
        ]);

        activity('publikasi')->performedOn($article)->log('berita_dibuat');

        return redirect()->route('cms.berita.show', $article)->with('status', 'Berita disimpan sebagai draf.');
    }

    public function show(Article $article): View
    {
        $this->authorize('view', $article);

        $statusMap = [
            Article::DIAJUKAN => ['ajukan', 'Ajukan', 'info'],
            Article::REVIEW => ['review', 'Mulai Review', 'info'],
            Article::REVISI => ['revisi', 'Minta Revisi', 'warning'],
            Article::DISETUJUI => ['setujui', 'Setujui', 'success'],
            Article::DIJADWALKAN => ['jadwalkan', 'Jadwalkan', 'warning'],
            Article::PUBLISH => ['publikasikan', 'Publikasikan', 'primary'],
            Article::ARSIP => ['arsip', 'Arsipkan', 'neutral'],
        ];

        $isEditor = in_array(auth()->user()->role, $this->editorRoles, true);
        $isAuthor = $article->author_id === auth()->id();

        $actions = collect(Article::transitions()[$article->status] ?? [])
            ->map(fn ($target) => $statusMap[$target] ?? null)
            ->filter()
            ->filter(function ($a) use ($isEditor, $isAuthor) {
                return $a[0] === 'ajukan' ? ($isAuthor || $isEditor) : $isEditor;
            })
            ->map(fn ($a) => ['aksi' => $a[0], 'label' => $a[1], 'variant' => $a[2]])
            ->values();

        return view('pages.cms.berita.show', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.berita.index')],
                ['label' => $article->title],
            ],
            'article' => $article,
            'statusLabels' => Article::statusLabels(),
            'actions' => $actions,
        ]);
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        return view('pages.cms.berita.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.berita.index')],
                ['label' => 'Ubah '.$article->title],
            ],
            'editing' => true,
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $validated = $request->validate($this->rules($article->id));

        $featured = $article->featured_image;
        if ($request->hasFile('featured_image')) {
            if ($featured) {
                Storage::disk('public')->delete($featured);
            }
            $featured = $request->file('featured_image')->store('berita', 'public');
        }

        $article->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? $article->slug,
            'summary' => $validated['summary'] ?? null,
            'body' => $validated['body'],
            'category' => $validated['category'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'featured_image' => $featured,
        ]);

        activity('publikasi')->performedOn($article)->log('berita_diubah');

        return redirect()->route('cms.berita.show', $article)->with('status', 'Berita diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        activity('publikasi')->performedOn($article)->log('berita_dihapus');

        return redirect()->route('cms.berita.index')->with('status', 'Berita dihapus.');
    }

    public function transition(Request $request, Article $article, string $aksi): RedirectResponse
    {
        $target = match ($aksi) {
            'ajukan' => Article::DIAJUKAN,
            'review' => Article::REVIEW,
            'revisi' => Article::REVISI,
            'setujui' => Article::DISETUJUI,
            'jadwalkan' => Article::DIJADWALKAN,
            'publikasikan' => Article::PUBLISH,
            'arsip' => Article::ARSIP,
            default => null,
        };

        abort_unless($target, 404);
        $this->authorizeTransition($article, $aksi);

        $scheduledAt = $request->input('scheduled_at');
        $article->transitionTo($target, $scheduledAt, auth()->id());

        activity('publikasi')->performedOn($article)->log('berita_status_'.$aksi);

        return back()->with('status', 'Status berita diubah menjadi '.strtolower($article->statusLabel()).'.');
    }

    protected function authorizeTransition(Article $article, string $aksi): void
    {
        $isEditor = in_array(auth()->user()->role, $this->editorRoles, true);
        $isAuthor = $article->author_id === auth()->id();

        if ($aksi === 'ajukan') {
            abort_unless($isAuthor || $isEditor, 403);
        } else {
            abort_unless($isEditor, 403);
        }
    }

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'unique:articles,slug,'.($ignoreId ?? 'NULL')],
            'summary' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug;
        $i = 2;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
