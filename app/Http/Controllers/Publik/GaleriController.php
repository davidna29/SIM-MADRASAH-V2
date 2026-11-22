<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\MediaAlbum;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(): View
    {
        $albums = MediaAlbum::where('status', 'publik')
            ->whereDoesntHave('items', fn ($query) => $query->where('file_path', 'like', '%demo-%'))
            ->withCount(['items' => fn ($query) => $query->where(function ($items) {
                $items->whereNotNull('publication_approved_at')->where(function ($approved) {
                    $approved->where('tipe', 'video')->orWhere(fn ($photos) => $photos->where('tipe', 'foto')->where('file_path', 'not like', '%demo-%'));
                });
            })])
            ->with(['items' => fn ($query) => $query
                ->select(['id', 'album_id', 'tipe', 'file_path', 'publication_approved_at'])
                ->whereNotNull('publication_approved_at')
                ->where('tipe', 'foto')
                ->where('file_path', 'not like', '%demo-%')])
            ->latest()
            ->paginate(12);

        return view('pages.publik.galeri.index', [
            'albums' => $albums,
        ]);
    }

    public function show(MediaAlbum $album): View
    {
        $items = $album->items()
            ->whereNotNull('publication_approved_at')
            ->where(function ($query) {
                $query->where('tipe', 'video')->orWhere(fn ($photos) => $photos->where('tipe', 'foto')->where('file_path', 'not like', '%demo-%'));
            })
            ->get();

        $hasDemoItems = $album->items()->where('file_path', 'like', '%demo-%')->exists();
        abort_unless($album->status === 'publik' && ! $hasDemoItems, 404);

        return view('pages.publik.galeri.show', [
            'album' => $album,
            'items' => $items,
            'photos' => $items->where('tipe', 'foto')->values(),
            'videos' => $items->where('tipe', 'video')->values(),
        ]);
    }
}
