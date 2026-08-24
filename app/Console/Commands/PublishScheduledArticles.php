<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class PublishScheduledArticles extends Command
{
    protected $signature = 'berita:publish-terjadwal';

    protected $description = 'Terbitkan artikel berstatus Dijadwalkan yang waktunya telah tiba';

    public function handle(): int
    {
        $count = 0;

        Article::where('status', Article::DIJADWALKAN)
            ->where('scheduled_at', '<=', now())
            ->get()
            ->each(function (Article $article) use (&$count) {
                $article->transitionTo(Article::PUBLISH, null, $article->reviewer_id);
                $count++;
            });

        $this->info("{$count} artikel diterbitkan.");

        return self::SUCCESS;
    }
}
