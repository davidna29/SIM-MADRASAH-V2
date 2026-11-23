<?php

namespace Tests\Feature;

use App\Models\PublicPage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegalDocumentSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'madrasah_name' => 'Madrasah Ibtidaiyah Nahdlatul Ulama',
            'madrasah_jenjang' => 'MI',
            'madrasah_status' => 'swasta',
            'madrasah_akreditasi' => 'terakreditasi',
            'madrasah_nilai_akreditasi' => 'A',
        ], $overrides);
    }

    public function test_admin_uploads_legal_pdf_and_public_profile_shows_download(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin']);

        PublicPage::create([
            'slug' => 'profil',
            'title' => 'Profil Madrasah',
            'body' => 'Profil singkat.',
            'status' => PublicPage::PUBLISHED,
        ]);

        $this->actingAs($admin)
            ->put(route('pengaturan.update'), $this->payload([
                'madrasah_file_akreditasi' => UploadedFile::fake()->create('akreditasi.pdf', 300, 'application/pdf'),
                'madrasah_file_sk_pendirian' => UploadedFile::fake()->create('sk.pdf', 200, 'application/pdf'),
            ]))
            ->assertRedirect();

        Storage::disk('public')->assertExists('settings/legalitas/sertifikat-akreditasi.pdf');
        Storage::disk('public')->assertExists('settings/legalitas/sk-pendirian.pdf');

        $this->get(route('publik.profil'))
            ->assertOk()
            ->assertSee('Dokumen Legalitas')
            ->assertSee('Sertifikat Akreditasi')
            ->assertSee('SK Pendirian')
            ->assertSee('Menyusul')
            ->assertSee('sertifikat-akreditasi.pdf');
    }

    public function test_non_pdf_document_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->put(route('pengaturan.update'), $this->payload([
                'madrasah_file_akreditasi' => UploadedFile::fake()->image('akreditasi.jpg'),
            ]))
            ->assertSessionHasErrors('madrasah_file_akreditasi');
    }

    public function test_replacing_document_removes_old_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->put(route('pengaturan.update'), $this->payload([
            'madrasah_file_akreditasi' => UploadedFile::fake()->create('lama.pdf', 100, 'application/pdf'),
        ]));

        $this->actingAs($admin)->put(route('pengaturan.update'), $this->payload([
            'madrasah_file_akreditasi' => UploadedFile::fake()->create('baru.pdf', 100, 'application/pdf'),
        ]));

        $this->assertCount(1, Storage::disk('public')->files('settings/legalitas'));
    }

    public function test_admin_uploads_hero_photos(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->put(route('pengaturan.update'), $this->payload([
                'beranda_hero_kamad' => UploadedFile::fake()->image('kamad.jpg', 400, 600),
                'beranda_hero_group' => UploadedFile::fake()->image('guru.jpg', 1200, 500),
            ]))
            ->assertRedirect();

        Storage::disk('public')->assertExists('settings/beranda/kepala-madrasah.jpg');
        Storage::disk('public')->assertExists('settings/beranda/guru-bersama.jpg');
        $this->assertSame('settings/beranda/kepala-madrasah.jpg', Setting::get('beranda_hero_kamad'));
        $this->assertSame('settings/beranda/guru-bersama.jpg', Setting::get('beranda_hero_group'));
    }
}
