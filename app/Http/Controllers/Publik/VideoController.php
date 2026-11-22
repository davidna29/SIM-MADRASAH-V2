<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $videos = MediaItem::query()
            ->where('tipe', 'video')
            ->whereNotNull('video_url')
            ->whereNotNull('publication_approved_at')
            ->whereHas('album', fn ($query) => $query->where('status', 'publik'))
            ->with('album:id,title,slug')
            ->latest()
            ->paginate(12);

        return view('pages.publik.video', ['videos' => $videos]);
    }
}
