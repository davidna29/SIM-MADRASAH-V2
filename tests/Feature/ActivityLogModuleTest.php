<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_activity_log(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->get(route('activity-log.index'))->assertOk();
    }

    public function test_non_admin_cannot_access(): void
    {
        $roles = ['guru', 'orang_tua', 'siswa', 'kepala_madrasah', 'bendahara'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('activity-log.index'))->assertForbidden();
        }
    }

    public function test_activity_rows_are_listed_with_readable_text(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'name' => 'Admin Madrasah']);

        activity('keuangan')->by($admin)->log('spp_dibayar');
        activity('kesiswaan')->by($admin)->log('prestasi_dicatat');

        $response = $this->actingAs($admin)->get(route('activity-log.index'));

        $response->assertOk();
        $response->assertSee('mencatat pembayaran SPP');
        $response->assertSee('mencatat prestasi siswa');
        $response->assertSee('Admin Madrasah');
        $response->assertSee('Keuangan');
    }

    public function test_filter_by_log_name_and_search(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        activity('keuangan')->by($admin)->log('spp_dibayar');
        activity('akademik')->by($admin)->log('rapor_diterbitkan');

        $response = $this->actingAs($admin)->get(route('activity-log.index', ['log_name' => 'keuangan']));

        $response->assertOk();
        $response->assertSee('mencatat pembayaran SPP');
        $response->assertDontSee('menerbitkan rapor');

        $search = $this->actingAs($admin)->get(route('activity-log.index', ['q' => 'rapor']));
        $search->assertOk();
        $search->assertSee('menerbitkan rapor');
        $search->assertDontSee('mencatat pembayaran SPP');
    }

    public function test_activity_log_view_handles_empty(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->get(route('activity-log.index'))->assertOk()->assertSee('Belum ada aktivitas tercatat.');
    }
}
