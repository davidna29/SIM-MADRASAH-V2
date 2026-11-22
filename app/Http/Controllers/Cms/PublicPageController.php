<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\PublicPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', PublicPage::class);

        $pages = PublicPage::query()
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('q'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.cms.halaman.index', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi', 'href' => route('dashboard')],
                ['label' => 'Halaman Publik'],
            ],
            'pages' => $pages,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PublicPage::class);

        return view('pages.cms.halaman.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi', 'href' => route('dashboard')],
                ['label' => 'Halaman Publik', 'href' => route('cms.halaman.index')],
                ['label' => 'Tambah Halaman'],
            ],
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', PublicPage::class);

        $validated = $request->validate($this->rules());
        $slug = $this->uniqueSlug(Str::slug(($validated['slug'] ?? '') ?: $validated['title']));

        $page = PublicPage::create([
            ...$validated,
            'slug' => $slug,
            'featured_image' => $request->file('featured_image')?->store('halaman', 'public'),
            'created_by' => auth()->id(),
        ]);

        activity('publikasi')->performedOn($page)->log('halaman_publik_dibuat');

        return redirect()->route('cms.halaman.edit', $page)->with('status', 'Halaman disimpan. Periksa konten sebelum mengubah status menjadi publik.');
    }

    public function edit(PublicPage $publicPage): View
    {
        $this->authorize('update', $publicPage);

        return view('pages.cms.halaman.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi', 'href' => route('dashboard')],
                ['label' => 'Halaman Publik', 'href' => route('cms.halaman.index')],
                ['label' => 'Ubah '.$publicPage->title],
            ],
            'editing' => true,
            'page' => $publicPage,
        ]);
    }

    public function update(Request $request, PublicPage $publicPage): RedirectResponse
    {
        $this->authorize('update', $publicPage);

        $validated = $request->validate($this->rules($publicPage->id));
        $slug = $this->uniqueSlug(Str::slug(($validated['slug'] ?? '') ?: $validated['title']), $publicPage->id);
        $image = $publicPage->featured_image;

        if ($request->hasFile('featured_image')) {
            if ($image) {
                Storage::disk('public')->delete($image);
            }

            $image = $request->file('featured_image')->store('halaman', 'public');
        }

        $publicPage->update([...$validated, 'slug' => $slug, 'featured_image' => $image]);

        activity('publikasi')->performedOn($publicPage)->log('halaman_publik_diubah');

        return redirect()->route('cms.halaman.edit', $publicPage)->with('status', 'Halaman diperbarui.');
    }

    public function destroy(PublicPage $publicPage): RedirectResponse
    {
        $this->authorize('delete', $publicPage);

        if ($publicPage->featured_image) {
            Storage::disk('public')->delete($publicPage->featured_image);
        }

        $publicPage->delete();

        activity('publikasi')->performedOn($publicPage)->log('halaman_publik_dihapus');

        return redirect()->route('cms.halaman.index')->with('status', 'Halaman dihapus.');
    }

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:public_pages,slug,'.($ignoreId ?? 'NULL')],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:50000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,publik'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'halaman';
        $candidate = $base;
        $suffix = 2;

        while (PublicPage::query()->where('slug', $candidate)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
