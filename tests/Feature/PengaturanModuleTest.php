<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->guru = User::factory()->create(['role' => 'guru']);

        // Seed default settings
        Setting::set('madrasah_name', 'MTs Al-Ikhlas Mulia');
        Setting::set('madrasah_jenjang', 'MTs');
        Setting::set('madrasah_status', 'swasta');
        Setting::set('madrasah_akreditasi', 'terakreditasi');
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('pengaturan.index'));
        $response->assertOk();
        $response->assertSee('Pengaturan Sistem');
        $response->assertSee('Identitas');
    }

    public function test_guru_cannot_access(): void
    {
        $this->actingAs($this->guru);
        $response = $this->get(route('pengaturan.index'));
        $response->assertForbidden();
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $response = $this->get(route('pengaturan.index'));
        $response->assertRedirect('/login');
    }

    public function test_can_update_settings(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('pengaturan.update'), [
            'madrasah_name' => 'Madrasah Baru',
            'madrasah_jenjang' => 'MA',
            'madrasah_status' => 'negeri',
            'madrasah_nsm' => '99999999',
            'madrasah_npsn' => '88888888',
            'madrasah_jalan' => 'Jl. Baru No. 1',
            'madrasah_desa' => 'Desa Baru',
            'madrasah_kecamatan' => 'Kec. Baru',
            'madrasah_kabupaten' => 'Kota Baru',
            'madrasah_provinsi' => 'Jawa Barat',
            'madrasah_kode_pos' => '12345',
            'madrasah_phone' => '(022) 9999999',
            'madrasah_email' => 'baru@madrasah.sch.id',
            'madrasah_website' => 'https://baru.sch.id',
            'madrasah_sk_pendirian' => '001/SK/2026',
            'madrasah_tgl_sk_pendirian' => '2026-01-01',
            'madrasah_sk_operasional' => '002/SK/2026',
            'madrasah_akreditasi' => 'terakreditasi',
            'madrasah_nilai_akreditasi' => 'A',
            'madrasah_naungan' => 'Kementerian Agama',
            'madrasah_tahun_berdiri' => '2000',
            'madrasah_latitude' => '-6.9175',
            'madrasah_longitude' => '107.6191',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertEquals('Madrasah Baru', Setting::get('madrasah_name'));
        $this->assertEquals('MA', Setting::get('madrasah_jenjang'));
        $this->assertEquals('negeri', Setting::get('madrasah_status'));
        $this->assertEquals('99999999', Setting::get('madrasah_nsm'));
        $this->assertEquals('A', Setting::get('madrasah_nilai_akreditasi'));
    }

    public function test_update_requires_name(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('pengaturan.update'), [
            'madrasah_name' => '',
            'madrasah_jenjang' => 'MTs',
            'madrasah_status' => 'swasta',
            'madrasah_akreditasi' => 'belum',
        ]);

        $response->assertSessionHasErrors('madrasah_name');
    }

    public function test_settings_persist(): void
    {
        $this->actingAs($this->admin);

        $this->put(route('pengaturan.update'), [
            'madrasah_name' => 'Madrasah Tes',
            'madrasah_jenjang' => 'MI',
            'madrasah_status' => 'negeri',
            'madrasah_akreditasi' => 'belum',
        ]);

        // Fresh request — settings should still be there
        $this->assertEquals('Madrasah Tes', Setting::get('madrasah_name'));
        $this->assertEquals('MI', Setting::get('madrasah_jenjang'));
    }

    public function test_can_upload_logo(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $logo = UploadedFile::fake()->image('logo.png', 200, 200);

        $response = $this->put(route('pengaturan.update'), [
            'madrasah_name' => 'Madrasah Logo',
            'madrasah_jenjang' => 'MTs',
            'madrasah_status' => 'swasta',
            'madrasah_akreditasi' => 'belum',
            'madrasah_logo' => $logo,
        ]);

        $response->assertRedirect();
        $logoPath = Setting::get('madrasah_logo');
        $this->assertNotEmpty($logoPath);
        $this->assertStringStartsWith('settings/', $logoPath);
    }

    public function test_guru_cannot_update(): void
    {
        $this->actingAs($this->guru);

        $response = $this->put(route('pengaturan.update'), [
            'madrasah_name' => 'Hack',
            'madrasah_jenjang' => 'MTs',
            'madrasah_status' => 'swasta',
            'madrasah_akreditasi' => 'belum',
        ]);

        $response->assertForbidden();
        $this->assertEquals('MTs Al-Ikhlas Mulia', Setting::get('madrasah_name'));
    }
}
