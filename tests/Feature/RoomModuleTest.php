<?php

namespace Tests\Feature;

use App\Models\OrganizationalUnit;
use App\Models\Position;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_sarpras']);
        Position::create(['code' => 'WAKAMAD_SARPRAS', 'name' => 'Wakamad Sarpras']);
        OrganizationalUnit::create(['code' => 'SARPRAS', 'name' => 'Sarpras']);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ruang Tes',
            'type' => 'ruangan',
            'building' => 'Gedung A',
            'floor' => 'Lantai 1',
            'capacity' => 30,
            'employee_id' => null,
            'condition' => 'baik',
            'description' => null,
        ], $overrides);
    }

    public function test_admin_can_create_room(): void
    {
        $response = $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload());

        $response->assertRedirect();

        $this->assertDatabaseHas('rooms', [
            'name' => 'Ruang Tes',
            'code' => 'R-001',
            'type' => 'ruangan',
        ]);
    }

    public function test_room_code_is_auto_generated(): void
    {
        $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload(['name' => 'Ruang A']));
        $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload(['name' => 'Ruang B']));

        $this->assertDatabaseHas('rooms', ['name' => 'Ruang A', 'code' => 'R-001']);
        $this->assertDatabaseHas('rooms', ['name' => 'Ruang B', 'code' => 'R-002']);
    }

    public function test_invalid_condition_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload([
            'condition' => 'parah',
        ]));

        $response->assertSessionHasErrors('condition');
    }

    public function test_index_page_lists_rooms(): void
    {
        $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload(['name' => 'Lab Fisika', 'type' => 'laboratorium']));

        $response = $this->actingAs($this->admin)->get(route('ruangan.index', ['type' => 'laboratorium']));

        $response->assertOk();
        $response->assertSee('Lab Fisika');
    }

    public function test_wakamad_can_view_and_edit(): void
    {
        $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload());

        $room = Room::first();

        $this->actingAs($this->wakamad)->get(route('ruangan.show', $room))->assertOk();
        $this->actingAs($this->wakamad)->get(route('ruangan.edit', $room))->assertOk();
    }

    public function test_guru_cannot_access(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('ruangan.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('ruangan.create'))->assertForbidden();
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('ruangan.index'))->assertRedirect(route('login'));
    }

    public function test_wakamad_can_delete(): void
    {
        $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload());
        $room = Room::first();

        $this->actingAs($this->wakamad)->delete(route('ruangan.destroy', $room))->assertRedirect();
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_guru_cannot_delete(): void
    {
        $this->actingAs($this->admin)->post(route('ruangan.store'), $this->payload());
        $room = Room::first();

        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->delete(route('ruangan.destroy', $room))->assertForbidden();
    }
}
