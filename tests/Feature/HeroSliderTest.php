<?php

namespace Tests\Feature;

use App\Models\MediaAlbum;
use App\Models\MediaItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroSliderTest extends TestCase
{
    use RefreshDatabase;

    public function test_hero_shows_headmaster_group_and_approved_slider_items(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('settings/beranda/kepala-madrasah.jpg', 'kamad');
        Storage::disk('public')->put('settings/beranda/guru-bersama.jpg', 'guru');
        Storage::disk('public')->put('galeri/sorotan/kegiatan-1.jpg', 'satu');
        Storage::disk('public')->put('galeri/sorotan/kegiatan-2.jpg', 'dua');
        Storage::disk('public')->put('galeri/sorotan/belum.jpg', 'belum');

        Setting::set('beranda_hero_kamad', 'settings/beranda/kepala-madrasah.jpg');
        Setting::set('beranda_hero_group', 'settings/beranda/guru-bersama.jpg');

        $admin = User::factory()->create(['role' => 'super_admin']);
        $album = MediaAlbum::create([
            'title' => 'Sorotan Madrasah', 'slug' => 'sorotan-madrasah', 'status' => 'publik', 'created_by' => $admin->id,
        ]);

        foreach (['kegiatan-1.jpg', 'kegiatan-2.jpg'] as $index => $file) {
            MediaItem::create([
                'album_id' => $album->id,
                'tipe' => 'foto',
                'file_path' => 'galeri/sorotan/'.$file,
                'title' => 'Slide '.($index + 1),
                'sort_order' => $index + 1,
                'publication_approved_at' => now(),
            ]);
        }

        MediaItem::create([
            'album_id' => $album->id,
            'tipe' => 'foto',
            'file_path' => 'galeri/sorotan/belum.jpg',
            'title' => 'Belum disetujui',
            'sort_order' => 3,
        ]);

        $this->get(route('publik.beranda'))
            ->assertOk()
            ->assertSee('Kepala Madrasah')
            ->assertSee('Guru &amp; Tenaga Kependidikan', false)
            ->assertSee('kepala-madrasah.jpg')
            ->assertSee('guru-bersama.jpg')
            ->assertSee('kegiatan-1.jpg')
            ->assertSee('Slide 1')
            ->assertDontSee('belum.jpg')
            ->assertDontSee('Belum disetujui')
            ->assertSee('heroSlider(4)', false);
    }

    public function test_hero_falls_back_when_no_assets(): void
    {
        $this->get(route('publik.beranda'))
            ->assertOk()
            ->assertSee('Ruang informasi madrasah dan kegiatan belajar.');
    }
}
