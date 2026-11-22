<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\MediaAlbum;
use App\Models\PublicPage;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            ['loc' => route('publik.beranda'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.berita.index'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.agenda.index'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.galeri.index'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.video'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.profil'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.sambutan'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.sejarah'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.visi-misi'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.struktur'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.guru'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.sarpras'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.kontak'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('publik.privasi'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('ppdb.form'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('pindahan.form'), 'lastmod' => now()->toAtomString()],
        ]);

        $canonicalSlugs = ['beranda', 'profil', 'sambutan', 'sejarah', 'visi-misi', 'kontak', 'kebijakan-privasi', 'sarana-prasarana'];
        $urls = $urls->merge(PublicPage::published()->whereNotIn('slug', $canonicalSlugs)->get(['slug', 'updated_at'])->map(fn (PublicPage $page) => [
            'loc' => route('publik.halaman', $page),
            'lastmod' => $page->updated_at?->toAtomString(),
        ]));

        $urls = $urls->merge(Article::query()
            ->where('status', Article::PUBLISH)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get(['slug', 'updated_at'])
            ->map(fn (Article $article) => [
                'loc' => route('publik.berita.show', $article),
                'lastmod' => $article->updated_at?->toAtomString(),
            ]));

        $urls = $urls->merge(MediaAlbum::query()
            ->where('status', 'publik')
            ->whereHas('items', fn ($query) => $query->whereNotNull('publication_approved_at')->where(fn ($items) => $items->where('tipe', 'video')->orWhere(fn ($photos) => $photos->where('tipe', 'foto')->where('file_path', 'not like', '%demo-%'))))
            ->get(['slug', 'updated_at'])
            ->map(fn (MediaAlbum $album) => [
                'loc' => route('publik.galeri.show', $album),
                'lastmod' => $album->updated_at?->toAtomString(),
            ]));

        return response()->view('pages.publik.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
