<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkApproveArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function article(User $author, string $status, string $title): Article
    {
        return Article::create([
            'title' => $title,
            'slug' => str()->slug($title),
            'body' => 'Isi berita.',
            'status' => $status,
            'author_id' => $author->id,
        ]);
    }

    public function test_editor_bulk_approves_eligible_articles_only(): void
    {
        $editor = User::factory()->create(['role' => 'editor_berita']);
        $author = User::factory()->create(['role' => 'guru']);

        $diajukan = $this->article($author, Article::DIAJUKAN, 'Berita Diajukan');
        $review = $this->article($author, Article::REVIEW, 'Berita Review');
        $draft = $this->article($author, Article::DRAFT, 'Berita Draft');

        $this->actingAs($editor)
            ->post(route('cms.berita.bulk-setujui'), [
                'ids' => [$diajukan->id, $review->id, $draft->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame(Article::DISETUJUI, $diajukan->fresh()->status);
        $this->assertSame(Article::DISETUJUI, $review->fresh()->status);
        $this->assertSame(Article::DRAFT, $draft->fresh()->status);
        $this->assertSame($editor->id, $diajukan->fresh()->reviewer_id);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'publikasi', 'description' => 'berita_bulk_setujui']);
    }

    public function test_guru_cannot_bulk_approve(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $article = $this->article($guru, Article::DIAJUKAN, 'Berita Guru');

        $this->actingAs($guru)
            ->post(route('cms.berita.bulk-setujui'), ['ids' => [$article->id]])
            ->assertForbidden();

        $this->assertSame(Article::DIAJUKAN, $article->fresh()->status);
    }

    public function test_ids_are_required(): void
    {
        $editor = User::factory()->create(['role' => 'editor_berita']);

        $this->actingAs($editor)
            ->post(route('cms.berita.bulk-setujui'), ['ids' => []])
            ->assertSessionHasErrors('ids');
    }
}
