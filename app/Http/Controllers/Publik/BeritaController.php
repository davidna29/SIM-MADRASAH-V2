<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\View\View;

class BeritaController extends Controller
{
    public function index(): View
    {
        $articles = Article::where('status', Article::PUBLISH)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('pages.publik.berita.index', [
            'articles' => $articles,
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless(
            $article->status === Article::PUBLISH && $article->published_at && $article->published_at->lte(now()),
            404,
        );

        return view('pages.publik.berita.show', [
            'article' => $article->loadMissing('author'),
            'lainnya' => Article::where('status', Article::PUBLISH)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where('id', '!=', $article->id)
                ->orderByDesc('published_at')
                ->take(5)
                ->get(),
        ]);
    }
}
