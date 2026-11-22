<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Article;
use App\Models\MediaItem;
use App\Models\PublicPage;
use App\Models\Setting;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        $settings = Setting::getAll();
        $publishedArticles = Article::query()
            ->where('status', Article::PUBLISH)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
        $featuredArticle = (clone $publishedArticles)->latest('published_at')->first();

        $articles = (clone $publishedArticles)
            ->when($featuredArticle, fn ($query) => $query->whereKeyNot($featuredArticle->id))
            ->latest('published_at')
            ->take(3)
            ->get();

        $today = now()->toDateString();
        $agenda = Agenda::query()
            ->where('status', 'aktif')
            ->where('target', 'publik')
            ->whereDate('tampil_mulai', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('tampil_selesai')->orWhereDate('tampil_selesai', '>=', $today);
            })
            ->whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->take(3)
            ->get();

        $photos = MediaItem::query()
            ->where('tipe', 'foto')
            ->whereNotNull('file_path')
            ->where('file_path', 'not like', '%demo-%')
            ->whereNotNull('publication_approved_at')
            ->whereHas('album', fn ($query) => $query->where('status', 'publik'))
            ->with('album:id,title,slug')
            ->latest()
            ->take(4)
            ->get();

        return view('pages.publik.beranda', [
            'featuredArticle' => $featuredArticle,
            'articles' => $articles,
            'agenda' => $agenda,
            'photos' => $photos,
            'homePage' => PublicPage::published()->where('slug', 'beranda')->first(),
            'siteSettings' => $settings,
            'ppdbOpen' => $settings->get('ppdb_status', 'closed') === 'open',
            'mutasiOpen' => $settings->get('mutasi_status', 'closed') === 'open',
        ]);
    }
}
