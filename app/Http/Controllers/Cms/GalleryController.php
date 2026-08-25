<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\MediaAlbum;
use App\Models\MediaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MediaAlbum::class);

        $albums = MediaAlbum::withCount('items')
            ->when(request('status'), fn ($q, $v) => $q->where('status', $v))
            ->when(request('q'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.cms.galeri.index', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda'],
                ['label' => 'Galeri'],
            ],
            'albums' => $albums,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', MediaAlbum::class);

        return view('pages.cms.galeri.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.galeri.index')],
                ['label' => 'Tambah Album'],
            ],
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', MediaAlbum::class);

        $validated = $request->validate($this->albumRules());

        $album = MediaAlbum::create([
            ...$validated,
            'slug' => $this->uniqueSlug($validated['title']),
            'cover_image' => $request->hasFile('cover_image') ? $request->file('cover_image')->store('galeri', 'public') : null,
            'created_by' => auth()->id(),
        ]);

        activity('publikasi')->performedOn($album)->log('galeri_album_dibuat');

        return redirect()->route('cms.galeri.show', $album)->with('status', 'Album dibuat — silakan unggah foto atau tambahkan video.');
    }

    public function show(MediaAlbum $album): View
    {
        $this->authorize('view', $album);

        return view('pages.cms.galeri.show', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.galeri.index')],
                ['label' => $album->title],
            ],
            'album' => $album,
        ]);
    }

    public function edit(MediaAlbum $album): View
    {
        $this->authorize('update', $album);

        return view('pages.cms.galeri.form', [
            'roleLabel' => 'Editor Berita',
            'breadcrumb' => [
                ['label' => 'Publikasi & PPDB', 'href' => route('dashboard')],
                ['label' => 'Berita & Agenda', 'href' => route('cms.galeri.index')],
                ['label' => 'Ubah Album'],
            ],
            'editing' => true,
            'album' => $album,
        ]);
    }

    public function update(Request $request, MediaAlbum $album): RedirectResponse
    {
        $this->authorize('update', $album);

        $validated = $request->validate($this->albumRules());

        $cover = $album->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($cover && ! $album->items()->where('file_path', $cover)->exists()) {
                Storage::disk('public')->delete($cover);
            }
            $cover = $request->file('cover_image')->store('galeri', 'public');
        }

        $album->update([...$validated, 'cover_image' => $cover]);

        activity('publikasi')->performedOn($album)->log('galeri_album_diubah');

        return redirect()->route('cms.galeri.show', $album)->with('status', 'Album diperbarui.');
    }

    public function destroy(MediaAlbum $album): RedirectResponse
    {
        $this->authorize('delete', $album);

        foreach ($album->items as $item) {
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path);
            }
        }

        if ($album->cover_image) {
            Storage::disk('public')->delete($album->cover_image);
        }

        $album->delete();

        activity('publikasi')->performedOn($album)->log('galeri_album_dihapus');

        return redirect()->route('cms.galeri.index')->with('status', 'Album dihapus beserta seluruh isinya.');
    }

    public function uploadPhotos(Request $request, MediaAlbum $album): RedirectResponse
    {
        $this->authorize('update', $album);

        $request->validate([
            'photos' => ['required', 'array', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $sort = (int) $album->items()->max('sort_order');

        foreach ($request->file('photos') as $photo) {
            $album->items()->create([
                'tipe' => 'foto',
                'file_path' => $photo->store('galeri', 'public'),
                'caption' => $request->input('caption') ?: null,
                'sort_order' => ++$sort,
            ]);
        }

        // Foto pertama otomatis jadi cover bila album belum punya
        if (! $album->cover_image) {
            $first = $album->items()->where('tipe', 'foto')->orderBy('sort_order')->first();
            if ($first) {
                $album->update(['cover_image' => $first->file_path]);
            }
        }

        activity('publikasi')->performedOn($album)->log('galeri_foto_diunggah');

        return back()->with('status', 'Foto berhasil diunggah ke album.');
    }

    public function addVideo(Request $request, MediaAlbum $album): RedirectResponse
    {
        $this->authorize('update', $album);

        $validated = $request->validate([
            'video_url' => ['required', 'url', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $album->items()->create([
            'tipe' => 'video',
            'video_url' => $validated['video_url'],
            'caption' => $validated['caption'] ?? null,
            'sort_order' => (int) $album->items()->max('sort_order') + 1,
        ]);

        activity('publikasi')->performedOn($album)->log('galeri_video_ditambahkan');

        return back()->with('status', 'Tautan video ditambahkan ke album.');
    }

    public function destroyItem(Request $request, MediaAlbum $album, MediaItem $item): RedirectResponse
    {
        $this->authorize('update', $album);

        abort_unless($item->album_id === $album->id, 404);

        if ($item->file_path) {
            Storage::disk('public')->delete($item->file_path);

            if ($album->cover_image === $item->file_path) {
                $album->update(['cover_image' => null]);
            }
        }

        $item->delete();

        activity('publikasi')->performedOn($album)->log('galeri_item_dihapus');

        return back()->with('status', 'Item galeri dihapus.');
    }

    public function setCover(Request $request, MediaAlbum $album, MediaItem $item): RedirectResponse
    {
        $this->authorize('update', $album);

        abort_unless($item->album_id === $album->id, 404);
        abort_unless($item->tipe === 'foto' && $item->file_path, 422, 'Cover hanya bisa dari foto.');

        $album->update(['cover_image' => $item->file_path]);

        activity('publikasi')->performedOn($album)->log('galeri_cover_diubah');

        return back()->with('status', 'Cover album diperbarui.');
    }

    protected function albumRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:publik,privat'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (MediaAlbum::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
