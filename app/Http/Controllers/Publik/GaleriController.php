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
            ->withCount('items')
            ->latest()
            ->paginate(12);

        return view('pages.publik.galeri.index', [
            'albums' => $albums,
        ]);
    }

    public function show(MediaAlbum $album): View
    {
        abort_unless($album->status === 'publik', 404);

        return view('pages.publik.galeri.show', [
            'album' => $album,
            'items' => $album->items()->get(),
            'photos' => $album->photos()->get(),
            'videos' => $album->items()->where('tipe', 'video')->get(),
        ]);
    }
}
