<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\PublicPage;
use App\Models\Room;
use Illuminate\View\View;

class SarprasController extends Controller
{
    public function index(): View
    {
        return view('pages.publik.sarpras', [
            'page' => PublicPage::published()->where('slug', 'sarana-prasarana')->first(),
            'rooms' => Room::query()
                ->select(['id', 'name', 'type', 'building', 'floor', 'capacity', 'description'])
                ->where('condition', 'baik')
                ->orderBy('type')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
