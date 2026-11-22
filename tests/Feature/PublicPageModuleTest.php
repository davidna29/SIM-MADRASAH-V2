<?php

namespace Tests\Feature;

use App\Models\PublicPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $humas;

    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->humas = User::factory()->create(['role' => 'wakamad_humas']);
        $this->guru = User::factory()->create(['role' => 'guru']);
    }

    protected function pagePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Profil Madrasah',
            'slug' => 'profil-madrasah',
            'subtitle' => 'Informasi resmi madrasah',
            'summary' => 'Ringkasan profil.',
            'body' => 'Isi profil yang telah ditinjau.',
            'status' => 'draft',
        ], $overrides);
    }

    public function test_humas_can_create_and_publish_public_page(): void
    {
        $this->actingAs($this->humas)->get(route('cms.halaman.index'))->assertOk();
        $this->actingAs($this->humas)->get(route('cms.halaman.create'))->assertOk();

        $this->actingAs($this->humas)->post(route('cms.halaman.store'), $this->pagePayload())
            ->assertRedirect();

        $page = PublicPage::where('slug', 'profil-madrasah')->firstOrFail();
        $this->actingAs($this->humas)->get(route('cms.halaman.edit', $page))->assertOk();
        $this->assertSame(PublicPage::DRAFT, $page->status);
        $this->get(route('publik.halaman', $page))->assertNotFound();

        $this->actingAs($this->humas)->put(route('cms.halaman.update', $page), $this->pagePayload(['status' => 'publik']))
            ->assertRedirect();

        $this->get(route('publik.halaman', $page->fresh()))->assertOk()->assertSee('Isi profil yang telah ditinjau.');
    }

    public function test_unapproved_roles_cannot_manage_public_pages(): void
    {
        $this->actingAs($this->guru)->get(route('cms.halaman.index'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('cms.halaman.store'), $this->pagePayload())->assertForbidden();
    }

    public function test_duplicate_page_slug_is_made_unique(): void
    {
        $this->actingAs($this->humas)->post(route('cms.halaman.store'), $this->pagePayload());
        $this->actingAs($this->humas)->post(route('cms.halaman.store'), $this->pagePayload(['slug' => '', 'title' => 'Profil Madrasah']));

        $this->assertDatabaseHas('public_pages', ['slug' => 'profil-madrasah-2']);
    }
}
