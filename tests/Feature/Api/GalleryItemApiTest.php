<?php

namespace Tests\Feature\Api;

use App\Models\MediaAlbum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryItemApiTest extends ApiTestCase
{
    protected function album(string $status = 'privat'): MediaAlbum
    {
        $editor = $this->apiUser('editor_berita');

        return MediaAlbum::create([
            'title' => 'Album API Item',
            'slug' => 'album-api-item-'.$status,
            'status' => $status,
            'created_by' => $editor->id,
        ]);
    }

    public function test_editor_adds_photo_item_to_private_album(): void
    {
        Storage::fake('public');
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['cms:write']);
        $album = $this->album();

        $this->withHeaders($this->idempotentHeaders('galeri-item-foto-1', $this->authHeaders($token)))
            ->post(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Foto Kegiatan',
                'deskripsi' => 'Dokumentasi kegiatan madrasah.',
                'tipe' => 'foto',
                'file' => UploadedFile::fake()->image('kegiatan.jpg', 1200, 800),
            ])
            ->assertCreated()
            ->assertJsonPath('data.tipe', 'foto')
            ->assertJsonPath('data.title', 'Foto Kegiatan')
            ->assertJsonPath('data.publication_approved_at', null);

        $item = $album->items()->firstOrFail();
        $this->assertNull($item->publication_approved_at);
        $this->assertSame('Foto Kegiatan', $item->title);
        $this->assertSame('Dokumentasi kegiatan madrasah.', $item->description);
        Storage::disk('public')->assertExists($item->file_path);
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'api',
            'description' => 'api_galeri_item_ditambah',
        ]);
    }

    public function test_editor_adds_youtube_video_item(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['cms:write']);
        $album = $this->album();

        $this->withHeaders($this->idempotentHeaders('galeri-item-video-1', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Video Profil',
                'deskripsi' => 'Profil madrasah.',
                'tipe' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertCreated()
            ->assertJsonPath('data.tipe', 'video')
            ->assertJsonPath('data.video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->assertJsonPath('data.publication_approved_at', null);

        $this->withHeaders($this->idempotentHeaders('galeri-item-video-2', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Video Pendek',
                'tipe' => 'video',
                'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            ])
            ->assertCreated();

        $this->assertSame(2, $album->items()->where('tipe', 'video')->count());
    }

    public function test_non_youtube_video_url_is_rejected(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['cms:write']);
        $album = $this->album();

        foreach (['https://www.facebook.com/watch/?v=123', 'https://www.instagram.com/reel/abc/', 'https://www.tiktok.com/@misnu/video/123'] as $index => $url) {
            $this->withHeaders($this->idempotentHeaders('galeri-item-nonyt-'.$index, $this->authHeaders($token)))
                ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                    'title' => 'Video Sosial Media',
                    'tipe' => 'video',
                    'video_url' => $url,
                ])
                ->assertStatus(422)
                ->assertJsonPath('code', 'VALIDATION_ERROR');
        }

        $this->assertSame(0, $album->items()->count());
    }

    public function test_album_publik_is_rejected_with_409(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['cms:write']);
        $album = $this->album('publik');

        $this->withHeaders($this->idempotentHeaders('galeri-item-publik-1', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Video',
                'tipe' => 'video',
                'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            ])
            ->assertStatus(409)
            ->assertJsonPath('code', 'RECORD_ALREADY_PUBLISHED');

        $this->assertSame(0, $album->items()->count());
    }

    public function test_file_or_video_url_is_required(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['cms:write']);
        $album = $this->album();

        $this->withHeaders($this->idempotentHeaders('galeri-item-kosong-1', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Tanpa Media',
                'tipe' => 'video',
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonValidationErrors('video_url');

        $this->withHeaders($this->idempotentHeaders('galeri-item-kosong-2', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Tanpa File',
                'tipe' => 'foto',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_idempotency_key_is_required(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['cms:write']);
        $album = $this->album();

        $this->withHeaders($this->authHeaders($token))
            ->post(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Tanpa Key',
                'tipe' => 'foto',
                'file' => UploadedFile::fake()->image('foto.jpg'),
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'IDEMPOTENCY_KEY_REQUIRED');
    }

    public function test_scope_cms_write_is_required(): void
    {
        $editor = $this->apiUser('editor_berita');
        $token = $this->bearer($editor, ['website:read']);
        $album = $this->album();

        $this->withHeaders($this->idempotentHeaders('galeri-item-scope-1', $this->authHeaders($token)))
            ->postJson(route('api.v1.cms.galeri.item.store', $album), [
                'title' => 'Video',
                'tipe' => 'video',
                'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            ])
            ->assertForbidden();
    }
}
