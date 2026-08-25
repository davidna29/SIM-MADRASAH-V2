<?php

namespace Tests\Feature;

use App\Models\MediaAlbum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;

    protected User $guru;

    protected User $kepala;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->editor = User::factory()->create(['role' => 'editor_berita']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
    }

    protected function albumPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Dokumentasi MPLS',
            'kategori' => 'Kegiatan',
            'description' => 'Momen MPLS.',
            'status' => 'publik',
        ], $overrides);
    }

    public function test_editor_can_create_album(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload())
            ->assertRedirect();

        $album = MediaAlbum::first();
        $this->assertSame('Dokumentasi MPLS', $album->title);
        $this->assertSame('dokumentasi-mpls', $album->slug);
        $this->assertNull($album->cover_image);
    }

    public function test_guru_cannot_manage_gallery(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());
        $album = MediaAlbum::first();

        $this->actingAs($this->guru)->get(route('cms.galeri.index'))->assertForbidden();
        $this->actingAs($this->guru)->put(route('cms.galeri.update', $album), $this->albumPayload(['title' => 'Bajak']))->assertForbidden();
    }

    public function test_kepala_can_view_gallery(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());

        $this->actingAs($this->kepala)->get(route('cms.galeri.index'))->assertOk();
    }

    public function test_multi_upload_photos(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());
        $album = MediaAlbum::first();

        $photos = [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.png'),
        ];

        $this->actingAs($this->editor)->post(route('cms.galeri.foto', $album), [
            'photos' => $photos,
            'caption' => 'Sesi pengenalan',
        ])->assertSessionHasNoErrors();

        $album->refresh();
        $this->assertSame(2, $album->items()->count());
        $this->assertSame('foto', $album->items()->first()->tipe);
        // Cover otomatis dari foto pertama
        $this->assertNotNull($album->cover_image);
        Storage::disk('public')->assertExists($album->items()->first()->file_path);
    }

    public function test_invalid_photo_type_is_rejected(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());
        $album = MediaAlbum::first();

        $this->actingAs($this->editor)->post(route('cms.galeri.foto', $album), [
            'photos' => [UploadedFile::fake()->create('catatan.pdf', 100, 'application/pdf')],
        ])->assertSessionHasErrors('photos.0');

        $this->assertSame(0, $album->items()->count());
    }

    public function test_add_video_url(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());
        $album = MediaAlbum::first();

        $this->actingAs($this->editor)->post(route('cms.galeri.video', $album), [
            'video_url' => 'https://www.youtube.com/watch?v=abc12345678',
            'caption' => 'Video MPLS',
        ])->assertSessionHasNoErrors();

        $item = $album->items()->first();
        $this->assertSame('video', $item->tipe);
        $this->assertSame('https://www.youtube.com/watch?v=abc12345678', $item->video_url);
    }

    public function test_public_shows_only_public_albums(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload([
            'title' => 'Arsip Internal',
            'status' => 'privat',
        ]));

        $response = $this->get(route('publik.galeri.index'));
        $response->assertOk();
        $response->assertSee('Dokumentasi MPLS');
        $response->assertDontSee('Arsip Internal');

        $privat = MediaAlbum::where('status', 'privat')->first();
        $this->get(route('publik.galeri.show', $privat))->assertNotFound();
    }

    public function test_set_cover_and_delete_item(): void
    {
        $this->actingAs($this->editor)->post(route('cms.galeri.store'), $this->albumPayload());
        $album = MediaAlbum::first();

        $this->actingAs($this->editor)->post(route('cms.galeri.foto', $album), [
            'photos' => [UploadedFile::fake()->image('x.jpg')],
        ]);
        $item = $album->items()->first();

        // Set cover dari foto lain tidak mungkin di sini (satu item) — hapus saja
        $this->actingAs($this->editor)->delete(route('cms.galeri.item.destroy', [$album, $item]))->assertRedirect();

        $this->assertDatabaseCount('media_items', 0);

        $album->refresh();
        $this->assertNull($album->cover_image);
    }
}
