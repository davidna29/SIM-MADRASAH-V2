<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CmsArticleIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_table_shows_publish_date(): void
    {
        $editor = User::factory()->create(['role' => 'editor_berita']);

        Article::create([
            'title' => 'Berita Sudah Terbit',
            'slug' => 'berita-sudah-terbit',
            'body' => 'Isi berita.',
            'status' => Article::PUBLISH,
            'author_id' => $editor->id,
            'published_at' => Carbon::parse('2026-03-10 09:30:00'),
        ]);

        $this->actingAs($editor)
            ->get(route('cms.berita.index'))
            ->assertOk()
            ->assertSee('Terbit')
            ->assertSee('10 Mar 2026 09:30');
    }
}
