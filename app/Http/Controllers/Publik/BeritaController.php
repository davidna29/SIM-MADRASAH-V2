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
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('pages.publik.berita.index', [
            'articles' => $articles,
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === Article::PUBLISH, 404);

        return view('pages.publik.berita.show', [
            'article' => $article,
            'lainnya' => Article::where('status', Article::PUBLISH)
                ->where('id', '!=', $article->id)
                ->orderByDesc('published_at')
                ->take(5)
                ->get(),
        ]);
    }
}
