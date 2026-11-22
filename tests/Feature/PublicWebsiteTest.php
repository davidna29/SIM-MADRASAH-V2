<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Employee;
use App\Models\MediaAlbum;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\PublicPage;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_is_the_public_homepage(): void
    {
        $response = $this->get(route('publik.beranda'));

        $response->assertOk()
            ->assertSee('Berita, agenda, dan informasi resmi madrasah')
            ->assertSee(route('ppdb.form'))
            ->assertDontSee('MTs Al-Ikhlas Mulia');
    }

    public function test_public_information_and_admission_pages_render_without_content(): void
    {
        Setting::set('ppdb_status', 'closed');
        Setting::set('mutasi_status', 'closed');

        foreach ([
            'publik.profil',
            'publik.sambutan',
            'publik.sejarah',
            'publik.visi-misi',
            'publik.struktur',
            'publik.guru',
            'publik.sarpras',
            'publik.kontak',
            'publik.privasi',
            'publik.video',
            'publik.berita.index',
            'publik.agenda.index',
            'publik.galeri.index',
        ] as $routeName) {
            $url = route($routeName);
            $response = $this->get($url);

            $this->assertSame(200, $response->status(), 'Route failed: '.$routeName.' ('.$url.'); response: '.substr(strip_tags($response->getContent()), 0, 300));
        }
    }

    public function test_homepage_and_news_only_show_published_articles(): void
    {
        $published = Article::create([
            'title' => 'Kegiatan Madrasah Terbaru',
            'slug' => 'kegiatan-madrasah-terbaru',
            'summary' => 'Ringkasan berita resmi.',
            'body' => 'Isi berita yang sudah dipublikasikan.',
            'status' => Article::PUBLISH,
            'published_at' => now()->subDay(),
        ]);
        Article::create([
            'title' => 'Draft Internal',
            'slug' => 'draft-internal',
            'body' => 'Konten yang belum boleh tampil.',
            'status' => Article::DRAFT,
        ]);
        Article::create([
            'title' => 'Berita Terjadwal',
            'slug' => 'berita-terjadwal',
            'body' => 'Belum waktunya ditayangkan.',
            'status' => Article::PUBLISH,
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('publik.beranda'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('Draft Internal')
            ->assertDontSee('Berita Terjadwal');

        $this->get(route('publik.berita.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('Draft Internal')
            ->assertDontSee('Berita Terjadwal');
    }

    public function test_only_published_public_pages_are_accessible(): void
    {
        $published = PublicPage::create([
            'title' => 'Sejarah Madrasah',
            'slug' => 'sejarah-publik',
            'body' => 'Informasi sejarah yang telah ditinjau.',
            'status' => PublicPage::PUBLISHED,
        ]);
        $draft = PublicPage::create([
            'title' => 'Catatan Internal',
            'slug' => 'catatan-internal',
            'body' => 'Belum diterbitkan.',
            'status' => PublicPage::DRAFT,
        ]);

        $this->get(route('publik.halaman', $published))->assertOk()->assertSee($published->body);
        $this->get(route('publik.halaman', $draft))->assertNotFound();
    }

    public function test_teacher_directory_only_shows_opted_in_names_without_personal_identifiers(): void
    {
        $unit = OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
        $position = Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        $person = Person::create([
            'nik' => '3508990000000001',
            'name' => 'Guru Publik',
            'gender' => 'P',
            'religion' => 'Islam',
            'phone' => '081234567890',
            'email' => 'private@example.test',
        ]);
        Employee::create([
            'person_id' => $person->id,
            'organizational_unit_id' => $unit->id,
            'position_id' => $position->id,
            'nip' => '199001012019031001',
            'employee_status' => 'pns',
            'status' => 'aktif',
            'show_on_website' => true,
        ]);

        $response = $this->get(route('publik.guru'));

        $response->assertOk()
            ->assertSee('Guru Publik')
            ->assertSee('Guru Mata Pelajaran')
            ->assertDontSee('199001012019031001')
            ->assertDontSee('3508990000000001')
            ->assertDontSee('private@example.test')
            ->assertDontSee('081234567890');
    }

    public function test_employee_is_hidden_from_public_directory_until_opted_in(): void
    {
        $unit = OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
        $position = Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        $person = Person::create(['nik' => '3508990000000002', 'name' => 'Nama Belum Diizinkan', 'gender' => 'L', 'religion' => 'Islam']);
        Employee::create([
            'person_id' => $person->id,
            'organizational_unit_id' => $unit->id,
            'position_id' => $position->id,
            'employee_status' => 'honor',
            'status' => 'aktif',
        ]);

        $this->get(route('publik.guru'))->assertOk()->assertDontSee('Nama Belum Diizinkan');
    }

    public function test_public_facilities_only_show_rooms_marked_in_good_condition(): void
    {
        Room::create(['code' => 'R-001', 'name' => 'Perpustakaan', 'type' => 'ruangan', 'condition' => 'baik', 'capacity' => 30]);
        Room::create(['code' => 'R-002', 'name' => 'Ruang Perbaikan', 'type' => 'ruangan', 'condition' => 'rusak_ringan', 'capacity' => 15]);

        $this->get(route('publik.sarpras'))
            ->assertOk()
            ->assertSee('Perpustakaan')
            ->assertDontSee('Ruang Perbaikan')
            ->assertDontSee('R-001');
    }

    public function test_demo_gallery_assets_are_not_displayed_publicly(): void
    {
        $album = MediaAlbum::create([
            'title' => 'Album Placeholder',
            'slug' => 'album-placeholder',
            'status' => 'publik',
        ]);
        $album->items()->create(['tipe' => 'foto', 'file_path' => 'galeri/demo-placeholder.png']);

        $this->get(route('publik.galeri.index'))->assertOk()->assertDontSee('Album Placeholder');
        $this->get(route('publik.galeri.show', $album))->assertNotFound();
    }

    public function test_video_page_only_shows_videos_in_public_albums(): void
    {
        $publicAlbum = MediaAlbum::create(['title' => 'Video Publik', 'slug' => 'video-publik', 'status' => 'publik']);
        $privateAlbum = MediaAlbum::create(['title' => 'Video Privat', 'slug' => 'video-privat', 'status' => 'privat']);
        $publicAlbum->items()->create([
            'tipe' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=abc12345678',
            'caption' => 'Video yang boleh ditonton',
            'publication_approved_at' => now(),
        ]);
        $privateAlbum->items()->create(['tipe' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=def12345678', 'caption' => 'Video internal']);

        $this->get(route('publik.video'))
            ->assertOk()
            ->assertSee('Video yang boleh ditonton')
            ->assertDontSee('Video internal')
            ->assertSee('youtube-nocookie.com', false);
    }

    public function test_contact_page_uses_configured_contact_information(): void
    {
        Setting::set('madrasah_phone', '021-123456');
        Setting::set('madrasah_email', 'info@madrasah.test');
        Setting::set('madrasah_jalan', 'Jalan Pendidikan');

        $this->get(route('publik.kontak'))
            ->assertOk()
            ->assertSee('021-123456')
            ->assertSee('info@madrasah.test')
            ->assertSee('Jalan Pendidikan');
    }

    public function test_privacy_page_and_sitemap_are_public(): void
    {
        $this->get(route('publik.privasi'))->assertOk()->assertSee('Data yang dimasukkan');
        $this->get(route('publik.sitemap'))->assertOk()->assertSee(route('publik.beranda'), false);
        $this->get(route('login'))->assertOk();
    }
}
