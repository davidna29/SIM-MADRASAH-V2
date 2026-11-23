<?php

namespace Tests\Feature\Api;

use App\Models\Agenda;
use App\Models\Article;
use App\Models\MediaAlbum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CmsApiTest extends ApiTestCase
{
    public function test_editor_creates_article_draft_and_updates_it(): void
    {
        Storage::fake('public');
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);

        $payload = [
            'title' => 'Berita API',
            'body' => 'Isi berita dari API.',
            'category' => 'Kegiatan',
            'featured_image' => UploadedFile::fake()->image('berita.jpg', 800, 600),
        ];

        $this->withHeaders($this->idempotentHeaders('cms-key-0001', $this->authHeaders($token)))
            ->post(route('api.v1.cms.berita.store'), $payload)
            ->assertCreated()
            ->assertJsonPath('data.status', Article::DRAFT)
            ->assertJsonPath('data.title', 'Berita API');

        $article = Article::firstOrFail();
        $this->assertSame(Article::DRAFT, $article->status);
        Storage::disk('public')->assertExists($article->featured_image);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'api', 'description' => 'api_berita_dibuat']);

        $this->withHeaders($this->idempotentHeaders('cms-key-0001-update', $this->authHeaders($token)))
            ->putJson(route('api.v1.cms.berita.update', $article), [
                'title' => 'Berita API Diubah',
                'body' => 'Isi yang diperbarui.',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Berita API Diubah');

        $article->update(['status' => Article::PUBLISH, 'published_at' => now()]);

        $this->withHeaders($this->idempotentHeaders('cms-key-0001-publish-check', $this->authHeaders($token)))
            ->putJson(route('api.v1.cms.berita.update', $article), [
                'title' => 'Tidak Boleh',
                'body' => 'Isi.',
            ])
            ->assertStatus(409)
            ->assertJsonPath('code', 'RECORD_ALREADY_PUBLISHED');
    }

    public function test_agenda_and_gallery_are_created_non_public(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);

        $this->withHeaders($this->idempotentHeaders('cms-key-0002', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.agenda.store'), [
                'title' => 'Agenda API',
                'jenis' => 'agenda',
                'target' => 'publik',
                'tampil_mulai' => now()->toDateString(),
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'arsip');

        $this->assertSame('arsip', Agenda::firstOrFail()->status);

        $this->withHeaders($this->idempotentHeaders('cms-key-0003', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.store'), [
                'title' => 'Album API',
                'description' => 'Dokumentasi.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'privat');

        $this->assertSame('privat', MediaAlbum::firstOrFail()->status);
    }

    public function test_cms_post_requires_idempotency_and_rejects_status_override(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);

        $this->withHeaders($this->authHeaders($token))
            ->postJson(route('api.v1.cms.berita.store'), ['title' => 'Tanpa Key', 'body' => 'Isi'])
            ->assertStatus(422)
            ->assertJsonPath('code', 'IDEMPOTENCY_KEY_REQUIRED');

        $this->withHeaders($this->idempotentHeaders('cms-key-0004', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.berita.store'), [
                'title' => 'Status Publish',
                'body' => 'Isi',
                'status' => 'publish',
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR');

        $this->assertSame(0, Article::count());
    }

    protected function publishableArticle(string $status = Article::DISETUJUI): Article
    {
        $editor = $this->apiUser('editor_berita');

        return Article::create([
            'title' => 'Berita Siap Terbit API',
            'slug' => 'berita-siap-terbit-api',
            'body' => 'Isi berita siap terbit.',
            'status' => $status,
            'author_id' => $editor->id,
        ]);
    }

    public function test_api_publishes_article_with_manual_date(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:30:00'));
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);
        $article = $this->publishableArticle();
        $manualDate = '2026-03-01 14:15:00';

        $this->withHeaders($this->idempotentHeaders('cms-key-pub-manual', $this->authHeaders($token)))
            ->putJson(route('api.v1.cms.berita.update', $article), [
                'title' => 'Berita Terbit API',
                'body' => 'Isi berita yang diterbitkan.',
                'status' => Article::PUBLISH,
                'published_at' => $manualDate,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', Article::PUBLISH)
            ->assertJsonPath('data.published_at', Carbon::parse($manualDate)->toIso8601String());

        $article->refresh();
        $this->assertSame(Article::PUBLISH, $article->status);
        $this->assertTrue($article->published_at->equalTo(Carbon::parse($manualDate)));
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'api',
            'description' => 'api_berita_dipublikasikan',
        ]);
    }

    public function test_api_publishes_article_without_date_using_now(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:30:00'));
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);
        $article = $this->publishableArticle(Article::DIJADWALKAN);

        $this->withHeaders($this->idempotentHeaders('cms-key-pub-now', $this->authHeaders($token)))
            ->putJson(route('api.v1.cms.berita.update', $article), [
                'title' => 'Berita Terbit API',
                'body' => 'Isi berita yang diterbitkan.',
                'status' => Article::PUBLISH,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', Article::PUBLISH)
            ->assertJsonPath('data.published_at', now()->toIso8601String());

        $this->assertTrue($article->fresh()->published_at->equalTo(now()));
    }

    public function test_api_rejects_future_publish_date(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:30:00'));
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);
        $article = $this->publishableArticle();

        $this->withHeaders($this->idempotentHeaders('cms-key-pub-future', $this->authHeaders($token)))
            ->putJson(route('api.v1.cms.berita.update', $article), [
                'title' => 'Berita Terbit API',
                'body' => 'Isi berita yang diterbitkan.',
                'status' => Article::PUBLISH,
                'published_at' => '2026-03-11 09:30:00',
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR');

        $article->refresh();
        $this->assertSame(Article::DISETUJUI, $article->status);
        $this->assertNull($article->published_at);
    }

    public function test_api_cannot_publish_draft_directly(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read', 'cms:write']);
        $article = $this->publishableArticle(Article::DRAFT);

        $this->withHeaders($this->idempotentHeaders('cms-key-pub-draft', $this->authHeaders($token)))
            ->putJson(route('api.v1.cms.berita.update', $article), [
                'title' => 'Draft API',
                'body' => 'Isi draft.',
                'status' => Article::PUBLISH,
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'BUSINESS_RULE_VIOLATION');

        $this->assertSame(Article::DRAFT, $article->fresh()->status);
    }

    public function test_agenda_update_is_limited_to_archive_records(): void
    {
        $editor = $this->apiUser('editor_berita');
        $agenda = Agenda::create([
            'title' => 'Agenda Aktif',
            'jenis' => 'agenda',
            'target' => 'publik',
            'tampil_mulai' => now()->toDateString(),
            'status' => 'aktif',
            'created_by' => $editor->id,
        ]);
        $token = $this->bearer($editor, ['website:read', 'cms:write']);

        $this->withHeaders($this->authHeaders($token))
            ->putJson(route('api.v1.cms.agenda.update', $agenda), [
                'title' => 'Agenda Diubah',
                'jenis' => 'agenda',
                'target' => 'publik',
                'tampil_mulai' => now()->toDateString(),
            ])
            ->assertStatus(409)
            ->assertJsonPath('code', 'RECORD_ALREADY_PUBLISHED');
    }

    public function test_guru_scope_allows_article_draft_but_policy_blocks_gallery(): void
    {
        $guru = $this->apiUser('guru');
        $token = $this->bearer($guru, ['website:read', 'cms:write']);

        $this->withHeaders($this->idempotentHeaders('cms-key-guru-1', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.berita.store'), [
                'title' => 'Draft Guru',
                'body' => 'Isi dari guru.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', Article::DRAFT);

        $this->withHeaders($this->idempotentHeaders('cms-key-guru-2', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.store'), ['title' => 'Album Guru'])
            ->assertForbidden()
            ->assertJsonPath('code', 'FORBIDDEN');
    }
}
