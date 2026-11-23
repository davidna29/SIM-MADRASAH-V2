<?php

namespace Tests\Feature\Api;

use App\Models\Agenda;
use App\Models\Article;
use App\Models\MediaAlbum;
use Illuminate\Http\UploadedFile;
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
