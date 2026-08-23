<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Guru A',
            'nik' => '3508990000000001',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_place' => 'Malang',
            'birth_date' => '1990-01-01',
            'phone' => '081234567890',
            'email' => 'gurua@madrasah.sch.id',
            'nip' => '199001012019031001',
            'employee_status' => 'pns',
            'status' => 'aktif',
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'tmt' => '2019-03-01',
        ], $overrides);
    }

    public function test_admin_can_create_employee_with_history(): void
    {
        $response = $this->actingAs($this->admin)->post(route('pegawai.store'), $this->payload());

        $response->assertRedirect();

        $this->assertDatabaseHas('people', ['nik' => '3508990000000001']);
        $this->assertDatabaseHas('employees', ['nip' => '199001012019031001']);
        $this->assertDatabaseCount('employee_position_histories', 1);

        $employee = Employee::where('nip', '199001012019031001')->first();
        $this->assertSame('pengangkatan', $employee->positionHistories->first()->reason);
    }

    public function test_nik_and_nip_must_be_unique(): void
    {
        Person::create([
            'nik' => '3508990000000001', 'name' => 'Lama', 'gender' => 'L', 'religion' => 'Islam',
        ]);
        Employee::create([
            'person_id' => Person::first()->id,
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'nip' => '199001012019031001',
            'employee_status' => 'pns',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)
            ->from(route('pegawai.create'))
            ->post(route('pegawai.store'), $this->payload());

        $response->assertSessionHasErrors(['nik', 'nip']);
    }

    public function test_invalid_nik_length_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('pegawai.store'), $this->payload(['nik' => '12345']));

        $response->assertSessionHasErrors('nik');
    }

    public function test_teacher_cannot_access_employee_module(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->actingAs($teacher)->get(route('pegawai.index'))->assertForbidden();
        $this->actingAs($teacher)->get(route('pegawai.create'))->assertForbidden();
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('pegawai.index'))->assertRedirect(route('login'));
    }

    public function test_employee_update_records_position_history_on_mutation(): void
    {
        $person = Person::create([
            'nik' => '3508990000000002', 'name' => 'Test Guru B', 'gender' => 'P', 'religion' => 'Islam',
        ]);
        $employee = Employee::create([
            'person_id' => $person->id,
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'employee_status' => 'pppk',
            'status' => 'aktif',
            'tmt' => '2020-01-01',
        ]);

        $ben = Position::create(['code' => 'BENDAHARA', 'name' => 'Bendahara']);

        $response = $this->actingAs($this->admin)->put(route('pegawai.update', $employee), $this->payload([
            'nik' => '3508990000000002',
            'name' => 'Test Guru B',
            'position_id' => $ben->id,
            'nip' => null,
            'employee_status' => 'pppk',
        ]));

        $response->assertRedirect();
        $this->assertSame('mutasi', $employee->positionHistories()->latest()->first()->reason);
        $this->assertSame($ben->id, $employee->fresh()->position_id);
    }

    public function test_super_admin_can_soft_delete_employee(): void
    {
        $person = Person::create([
            'nik' => '3508990000000003', 'name' => 'Test Guru C', 'gender' => 'L', 'religion' => 'Islam',
        ]);
        $employee = Employee::create([
            'person_id' => $person->id,
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'employee_status' => 'honor',
            'status' => 'aktif',
        ]);

        $this->actingAs($this->admin)->delete(route('pegawai.destroy', $employee));

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }
}
