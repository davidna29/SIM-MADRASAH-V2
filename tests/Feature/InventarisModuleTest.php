<?php

namespace Tests\Feature;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMaintenance;
use App\Models\InventoryMutation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarisModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $tu;

    protected User $kepala;

    protected User $guru;

    protected InventoryCategory $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_sarpras']);
        $this->tu = User::factory()->create(['role' => 'tata_usaha']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->guru = User::factory()->create(['role' => 'guru']);

        $this->kategori = InventoryCategory::create(['name' => 'Elektronik']);
    }

    protected function makeItem(array $overrides = []): InventoryItem
    {
        static $i = 0;
        $i++;

        return InventoryItem::create(array_merge([
            'category_id' => $this->kategori->id,
            'code' => 'INV-202608-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'name' => 'Proyektor LCD',
            'quantity' => 2,
            'unit' => 'unit',
            'condition' => 'baik',
            'location' => 'Ruang Aula',
            'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ], $overrides));
    }

    protected function itemPayload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->kategori->id,
            'name' => 'AC Split 2 PK',
            'quantity' => 3,
            'unit' => 'unit',
            'condition' => 'baik',
            'location' => 'Ruang Kelas',
            'status' => 'tersedia',
        ], $overrides);
    }

    public function test_wakamad_can_create_item_with_auto_code(): void
    {
        $response = $this->actingAs($this->wakamad)->post(route('inventaris.store'), $this->itemPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'AC Split 2 PK',
            'code' => 'INV-'.now()->format('Ym').'-001',
        ]);
    }

    public function test_admin_can_update_item(): void
    {
        $item = $this->makeItem();

        $this->actingAs($this->admin)->put(route('inventaris.update', $item), $this->itemPayload(['name' => 'AC Baru']))
            ->assertRedirect();

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'name' => 'AC Baru']);
    }

    public function test_guru_cannot_access_inventory(): void
    {
        $item = $this->makeItem();

        $this->actingAs($this->guru)->get(route('inventaris.index'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('inventaris.store'), $this->itemPayload())->assertForbidden();
        $this->actingAs($this->guru)->get(route('inventaris.show', $item))->assertForbidden();
    }

    public function test_kepala_read_only_cannot_mutate(): void
    {
        $item = $this->makeItem();

        $this->actingAs($this->kepala)->get(route('inventaris.index'))->assertOk();
        $this->actingAs($this->kepala)->get(route('inventaris.show', $item))->assertOk();

        $this->actingAs($this->kepala)->post(route('inventaris.store'), $this->itemPayload())->assertForbidden();
        $this->actingAs($this->kepala)->post(route('inventaris.mutasi.store', $item), [
            'to_location' => 'Gudang', 'quantity' => 1, 'mutation_date' => now()->toDateString(),
        ])->assertForbidden();
    }

    public function test_tu_can_create_and_manage_but_not_delete(): void
    {
        $item = $this->makeItem();

        $this->actingAs($this->tu)->post(route('inventaris.store'), $this->itemPayload())->assertRedirect();

        // TU bisa mengajukan mutasi tapi tidak bisa menghapus barang
        $this->actingAs($this->tu)->delete(route('inventaris.destroy', $item))->assertForbidden();
    }

    public function test_mutation_workflow_and_location_update(): void
    {
        $item = $this->makeItem(['location' => 'Ruang Aula']);

        // TU ajukan mutasi
        $this->actingAs($this->tu)->post(route('inventaris.mutasi.store', $item), [
            'to_location' => 'Ruang Guru',
            'quantity' => 1,
            'mutation_date' => now()->toDateString(),
            'reason' => 'Kebutuhan',
        ])->assertRedirect();

        $mutation = InventoryMutation::first();
        $this->assertSame('pending', $mutation->status);
        $this->assertSame('Ruang Aula', $mutation->from_location);

        // Wakamad setujui → lokasi barang diperbarui
        $this->actingAs($this->wakamad)->post(route('inventaris.mutasi.approve', [$item, $mutation]))->assertRedirect();

        $this->assertDatabaseHas('inventory_mutations', ['id' => $mutation->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'location' => 'Ruang Guru']);
    }

    public function test_kepala_cannot_approve_mutation(): void
    {
        $item = $this->makeItem();
        $mutation = InventoryMutation::create([
            'item_id' => $item->id,
            'to_location' => 'Gudang',
            'quantity' => 1,
            'mutation_date' => now()->toDateString(),
            'status' => 'pending',
            'created_by' => $this->tu->id,
        ]);

        $this->actingAs($this->kepala)->post(route('inventaris.mutasi.approve', [$item, $mutation]))->assertForbidden();
    }

    public function test_maintenance_sets_item_in_maintenance_and_back_to_available(): void
    {
        $item = $this->makeItem();

        $this->actingAs($this->wakamad)->post(route('inventaris.perawatan.store', $item), [
            'type' => 'perbaikan',
            'description' => 'Servis AC',
            'cost' => 500000,
            'start_date' => now()->toDateString(),
            'technician' => 'Toko Jaya',
        ])->assertRedirect();

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'status' => 'dalam_perawatan']);

        $maintenance = InventoryMaintenance::first();
        $this->assertSame('berlangsung', $maintenance->status);

        // Selesaikan → barang kembali tersedia
        $this->actingAs($this->wakamad)->post(route('inventaris.perawatan.selesai', [$item, $maintenance]))->assertRedirect();

        $this->assertDatabaseHas('inventory_maintenances', ['id' => $maintenance->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'status' => 'tersedia']);
    }

    public function test_category_management(): void
    {
        $this->actingAs($this->admin)->post(route('inventaris.kategori.store'), [
            'name' => 'Olahraga',
        ])->assertRedirect();

        $this->assertDatabaseHas('inventory_categories', ['name' => 'Olahraga']);

        // Kategori berisi barang tidak bisa dihapus
        $this->makeItem();
        $this->actingAs($this->admin)->delete(route('inventaris.kategori.destroy', $this->kategori))
            ->assertSessionHasErrors('kategori');

        // Kategori kosong bisa dihapus
        $kosong = InventoryCategory::create(['name' => 'Kosong']);
        $this->actingAs($this->admin)->delete(route('inventaris.kategori.destroy', $kosong))->assertRedirect();
        $this->assertDatabaseMissing('inventory_categories', ['id' => $kosong->id]);
    }

    public function test_show_renders_details_mutations_and_maintenance(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->wakamad)->get(route('inventaris.show', $item));

        $response->assertOk();
        $response->assertSee('Proyektor LCD');
        $response->assertSee('Ajukan Mutasi');
        $response->assertSee('Catat Pemeliharaan / Perbaikan');
    }

    public function test_index_filters(): void
    {
        $this->makeItem(['name' => 'Proyektor', 'condition' => 'rusak_ringan']);
        $this->makeItem(['name' => 'AC', 'condition' => 'baik']);

        $response = $this->actingAs($this->admin)->get(route('inventaris.index', ['condition' => 'baik']));

        $response->assertOk();
        $response->assertSee('AC');
        $response->assertDontSee('Proyektor');
    }
}
