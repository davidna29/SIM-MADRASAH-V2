<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class CmsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;

    protected User $editor;

    protected User $humas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->editor = User::factory()->create(['role' => 'editor_berita']);
        $this->humas = User::factory()->create(['role' => 'wakamad_humas']);
    }

    protected function articlePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Berita Uji',
            'body' => 'Isi berita uji.',
            'category' => 'Kegiatan',
            'tags' => 'uji, demo',
            'summary' => 'Ringkasan uji.',
        ], $overrides);
    }

    public function test_guru_can_create_article_and_flow_through_approval(): void
    {
        $this->actingAs($this->guru)->post(route('cms.berita.store'), $this->articlePayload())
            ->assertRedirect();

        $article = Article::first();
        $this->assertSame(Article::DRAFT, $article->status);
        $this->assertSame($this->guru->id, $article->author_id);

        $this->actingAs($this->guru)->post(route('cms.berita.transition', [$article, 'ajukan']));
        $this->actingAs($this->editor)->post(route('cms.berita.transition', [$article, 'review']));
        $this->actingAs($this->editor)->post(route('cms.berita.transition', [$article, 'setujui']));
        $this->actingAs($this->editor)->post(route('cms.berita.transition', [$article, 'publikasikan']));

        $article->refresh();
        $this->assertSame(Article::PUBLISH, $article->status);
        $this->assertNotNull($article->published_at);
    }

    public function test_invalid_transition_is_blocked(): void
    {
        $this->actingAs($this->guru)->post(route('cms.berita.store'), $this->articlePayload());
        $article = Article::first();

        $this->actingAs($this->editor)->post(route('cms.berita.transition', [$article, 'publikasikan']))->assertStatus(422);
        $this->actingAs($this->editor)->post(route('cms.berita.transition', [$article, 'setujui']))->assertStatus(422);

        $article->refresh();
        $this->assertSame(Article::DRAFT, $article->status);
    }

    public function test_guru_cannot_edit_or_approve_others_article(): void
    {
        $this->actingAs($this->editor)->post(route('cms.berita.store'), $this->articlePayload());
        $article = Article::first();

        $this->actingAs($this->guru)->put(route('cms.berita.update', $article), $this->articlePayload(['title' => 'Dibajak']))->assertForbidden();
        $this->actingAs($this->guru)->post(route('cms.berita.transition', [$article, 'setujui']))->assertForbidden();
    }

    public function test_public_page_only_shows_published_articles(): void
    {
        $this->actingAs($this->editor)->post(route('cms.berita.store'), $this->articlePayload(['title' => 'Berita Terbit']));
        $this->actingAs($this->editor)->post(route('cms.berita.store'), $this->articlePayload(['title' => 'Berita Draf', 'body' => 'rahasia draf']));

        $terbit = Article::where('title', 'Berita Terbit')->first();
        $draft = Article::where('title', 'Berita Draf')->first();

        $terbit->update(['status' => Article::PUBLISH, 'published_at' => now()]);

        $response = $this->get(route('publik.berita.index'));
        $response->assertOk();
        $response->assertSee('Berita Terbit');
        $response->assertDontSee('Berita Draf');

        $this->get(route('publik.berita.show', $terbit))->assertOk();
        $this->get(route('publik.berita.show', $draft))->assertNotFound();
    }

    public function test_agenda_crud_and_public_display(): void
    {
        $this->actingAs($this->editor)->post(route('cms.agenda.store'), [
            'title' => 'Rapat Guru',
            'jenis' => 'agenda',
            'tanggal' => now()->addDays(2)->toDateString(),
            'waktu' => '14:00',
            'lokasi' => 'Aula',
            'penanggung_jawab' => 'Wakamad',
            'isi' => 'Rapat persiapan rapor',
            'target' => 'publik',
            'tampil_mulai' => now()->toDateString(),
            'status' => 'aktif',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('agenda', ['title' => 'Rapat Guru', 'status' => 'aktif']);

        $this->get(route('publik.agenda.index'))->assertOk()->assertSee('Rapat Guru');

        // Agenda arsip tidak tampil di publik
        Agenda::where('title', 'Rapat Guru')->update(['status' => 'arsip']);
        $this->get(route('publik.agenda.index'))->assertDontSee('Rapat Guru');
    }

    public function test_role_access_agenda_and_berita(): void
    {
        $this->actingAs($this->guru)->get(route('cms.agenda.index'))->assertForbidden();
        $this->actingAs($this->editor)->get(route('cms.agenda.index'))->assertOk();
        $this->actingAs($this->humas)->get(route('cms.berita.index'))->assertOk();
        $this->actingAs($this->guru)->get(route('cms.berita.index'))->assertOk();
    }

    protected function approvedArticle(): Article
    {
        return Article::create([
            'title' => 'Berita Siap Terbit',
            'slug' => 'berita-siap-terbit',
            'body' => 'Isi berita siap terbit.',
            'status' => Article::DISETUJUI,
            'author_id' => $this->editor->id,
        ]);
    }

    public function test_publish_without_date_uses_now(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:30:00'));
        $article = $this->approvedArticle();

        $this->actingAs($this->editor)
            ->post(route('cms.berita.transition', [$article, 'publikasikan']))
            ->assertRedirect();

        $article->refresh();
        $this->assertSame(Article::PUBLISH, $article->status);
        $this->assertTrue($article->published_at->equalTo(now()));

        $log = Activity::query()->where('description', 'berita_publish_manual')->latest('id')->firstOrFail();
        $this->assertFalse((bool) $log->properties['manual']);
        $this->assertSame(now()->toIso8601String(), $log->properties['published_at']);
    }

    public function test_publish_with_past_date_uses_given_date(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:30:00'));
        $article = $this->approvedArticle();
        $manualDate = '2026-03-01 14:15:00';

        $this->actingAs($this->editor)
            ->post(route('cms.berita.transition', [$article, 'publikasikan']), ['published_at' => $manualDate])
            ->assertRedirect();

        $article->refresh();
        $this->assertSame(Article::PUBLISH, $article->status);
        $this->assertTrue($article->published_at->equalTo(Carbon::parse($manualDate)));

        $log = Activity::query()->where('description', 'berita_publish_manual')->latest('id')->firstOrFail();
        $this->assertTrue((bool) $log->properties['manual']);
        $this->assertSame(Carbon::parse($manualDate)->toIso8601String(), $log->properties['published_at']);
    }

    public function test_publish_with_future_date_is_rejected(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:30:00'));
        $article = $this->approvedArticle();

        $this->actingAs($this->editor)
            ->post(route('cms.berita.transition', [$article, 'publikasikan']), ['published_at' => '2026-03-11 09:30:00'])
            ->assertStatus(422);

        $article->refresh();
        $this->assertSame(Article::DISETUJUI, $article->status);
        $this->assertNull($article->published_at);
    }

    public function test_scheduled_article_auto_publishes(): void
    {
        $article = Article::create([
            'title' => 'Terjadwal',
            'slug' => Str::slug('Terjadwal'),
            'body' => 'Isi',
            'status' => Article::DIJADWALKAN,
            'author_id' => $this->editor->id,
            'scheduled_at' => now()->subMinute(),
        ]);

        Artisan::call('berita:publish-terjadwal');

        $article->refresh();
        $this->assertSame(Article::PUBLISH, $article->status);
        $this->assertNotNull($article->published_at);
    }
}
