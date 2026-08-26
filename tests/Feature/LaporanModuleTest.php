<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kepala;
    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->guru = User::factory()->create(['role' => 'guru']);
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.index'));
        $response->assertOk();
    }

    public function test_kepala_madrasah_can_access(): void
    {
        $this->actingAs($this->kepala);
        $response = $this->get(route('laporan.index'));
        $response->assertOk();
    }

    public function test_guru_cannot_access(): void
    {
        $this->actingAs($this->guru);
        $response = $this->get(route('laporan.index'));
        $response->assertForbidden();
    }

    public function test_can_view_akademik(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.akademik'));
        $response->assertOk();
        $response->assertSee('Rekap Akademik');
    }

    public function test_can_view_kehadiran(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.kehadiran'));
        $response->assertOk();
    }

    public function test_can_view_keuangan(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.keuangan'));
        $response->assertOk();
    }

    public function test_can_view_kesiswaan(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.kesiswaan'));
        $response->assertOk();
    }

    public function test_can_view_tenaga(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.tenaga'));
        $response->assertOk();
    }

    public function test_can_view_perpustakaan(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.perpustakaan'));
        $response->assertOk();
    }

    public function test_can_export_csv(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('laporan.csv', 'akademik'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }
}
