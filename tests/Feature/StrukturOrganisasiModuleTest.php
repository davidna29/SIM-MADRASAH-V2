<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StrukturOrganisasiModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kurikulum']);
    }

    public function test_admin_can_create_unit(): void
    {
        $response = $this->actingAs($this->admin)->post(route('unit-kerja.store'), [
            'code' => 'BENDAHARA',
            'name' => 'Keuangan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organizational_units', ['code' => 'BENDAHARA']);
    }

    public function test_unit_code_must_be_unique(): void
    {
        OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);

        $response = $this->actingAs($this->admin)->post(route('unit-kerja.store'), [
            'code' => 'GURU',
            'name' => 'Guru Duplicate',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_cannot_delete_unit_with_employees(): void
    {
        $unit = OrganizationalUnit::create(['code' => 'TEST', 'name' => 'Test Unit']);

        Person::create(['nik' => '3508990000000099', 'name' => 'Pegawai Test', 'gender' => 'L', 'religion' => 'Islam']);
        $person = Person::first();
        Employee::create([
            'person_id' => $person->id,
            'organizational_unit_id' => $unit->id,
            'position_id' => null,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('unit-kerja.destroy', $unit));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('organizational_units', ['id' => $unit->id]);
    }

    public function test_admin_can_create_position(): void
    {
        $response = $this->actingAs($this->admin)->post(route('jabatan.store'), [
            'code' => 'OPERATOR',
            'name' => 'Operator Komputer',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('positions', ['code' => 'OPERATOR']);
    }

    public function test_position_code_must_be_unique(): void
    {
        Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mapel']);

        $response = $this->actingAs($this->admin)->post(route('jabatan.store'), [
            'code' => 'GURU_MAPEL',
            'name' => 'Guru Mapel Dup',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_cannot_delete_position_with_employees(): void
    {
        $pos = Position::create(['code' => 'TEST_POS', 'name' => 'Test Position']);

        Person::create(['nik' => '3508990000000098', 'name' => 'Pegawai Test 2', 'gender' => 'L', 'religion' => 'Islam']);
        $person = Person::where('nik', '3508990000000098')->first();
        Employee::create([
            'person_id' => $person->id,
            'position_id' => $pos->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('jabatan.destroy', $pos));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('positions', ['id' => $pos->id]);
    }

    public function test_struktur_page_lists_employees_by_unit(): void
    {
        $unit = OrganizationalUnit::create(['code' => 'STRTEST', 'name' => 'Unit Struktur']);

        $person = Person::create(['nik' => '3508990000000097', 'name' => 'Pegawai Struktur', 'gender' => 'L', 'religion' => 'Islam']);
        Employee::create([
            'person_id' => $person->id,
            'organizational_unit_id' => $unit->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get(route('struktur.index'));

        $response->assertOk();
        $response->assertSee('Unit Struktur');
        $response->assertSee('Pegawai Struktur');
    }

    public function test_guru_cannot_access(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('unit-kerja.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('jabatan.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('struktur.index'))->assertForbidden();
    }
}
