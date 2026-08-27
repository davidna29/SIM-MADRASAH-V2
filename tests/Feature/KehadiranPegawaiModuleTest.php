<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KehadiranPegawaiModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $tu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->tu = User::factory()->create(['role' => 'tata_usaha']);
        Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
    }

    protected function makeEmployee(string $name = 'Guru Tes', string $nip = '199001012019031001'): Employee
    {
        $person = Person::create([
            'nik' => '350899'.str_pad(substr($nip, -9), 9, '0'),
            'name' => $name,
            'gender' => 'L',
            'religion' => 'Islam',
        ]);

        return Employee::create([
            'person_id' => $person->id,
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'nip' => $nip,
            'employee_status' => 'honor',
            'status' => 'aktif',
        ]);
    }

    protected function payload(Employee $employee, array $overrides = []): array
    {
        return array_merge([
            'attendance_date' => '2026-08-25',
            'attendances' => [
                $employee->id => ['status' => 'hadir', 'clock_in' => '07:15', 'clock_out' => '13:45', 'note' => null],
            ],
        ], $overrides);
    }

    public function test_tu_can_record_employee_attendance(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee, [
            'attendances' => [$employee->id => ['status' => 'sakit', 'clock_in' => null, 'clock_out' => null, 'note' => 'Demam']],
        ]));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('employee_attendances', [
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-25',
            'status' => 'sakit',
            'note' => 'Demam',
        ]);
    }

    public function test_attendance_is_unique_per_employee_per_day(): void
    {
        $employee = $this->makeEmployee();

        $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee));
        $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee, [
            'attendances' => [$employee->id => ['status' => 'terlambat', 'clock_in' => '07:45', 'clock_out' => '13:45', 'note' => null]],
        ]));

        $this->assertSame(1, EmployeeAttendance::where('employee_id', $employee->id)->where('attendance_date', '2026-08-25')->count());
        $this->assertSame('terlambat', EmployeeAttendance::where('employee_id', $employee->id)->where('attendance_date', '2026-08-25')->first()->status);
    }

    public function test_kepala_madrasah_readonly(): void
    {
        $kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $employee = $this->makeEmployee();

        $this->actingAs($kepala)->get(route('pegawai.kehadiran.index'))->assertOk();
        $this->actingAs($kepala)->get(route('pegawai.kehadiran.rekap-bulanan'))->assertOk();
        $this->actingAs($kepala)->post(route('pegawai.kehadiran.store'), $this->payload($employee))->assertForbidden();
    }

    public function test_guru_cannot_access_employee_attendance(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('pegawai.kehadiran.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('pegawai.kehadiran.rekap-tahunan'))->assertForbidden();
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('pegawai.kehadiran.index'))->assertRedirect(route('login'));
    }

    public function test_index_page_lists_active_employees(): void
    {
        $employee = $this->makeEmployee('Guru Citra');

        $response = $this->actingAs($this->admin)->get(route('pegawai.kehadiran.index'));

        $response->assertOk();
        $response->assertSee('Guru Citra');
    }

    public function test_invalid_status_is_rejected(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($this->admin)->post(route('pegawai.kehadiran.store'), $this->payload($employee, [
            'attendances' => [$employee->id => ['status' => 'libur']],
        ]));

        $response->assertSessionHasErrors('attendances.'.$employee->id.'.status');
    }

    public function test_past_date_correction_by_tu(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee, [
            'attendance_date' => '2025-01-01',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('employee_attendances', [
            'employee_id' => $employee->id,
            'attendance_date' => '2025-01-01',
            'status' => 'hadir',
        ]);
    }

    public function test_rekap_bulanan_shows_tally_and_percentage(): void
    {
        $employee = $this->makeEmployee('Guru Rekap');

        // 1 hadir + 1 sakit dari 2 hari tercatat → 50%
        $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee, ['attendance_date' => '2026-08-25']));
        $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee, [
            'attendance_date' => '2026-08-26',
            'attendances' => [$employee->id => ['status' => 'sakit', 'clock_in' => null, 'clock_out' => null, 'note' => null]],
        ]));

        $response = $this->actingAs($this->admin)->get(route('pegawai.kehadiran.rekap-bulanan', ['month' => '2026-08']));

        $response->assertOk();
        $response->assertSee('Guru Rekap');
        $response->assertSee('50%');
    }

    public function test_rekap_tahunan_renders_with_employee(): void
    {
        $employee = $this->makeEmployee('Guru Tahunan');

        $this->actingAs($this->tu)->post(route('pegawai.kehadiran.store'), $this->payload($employee, ['attendance_date' => '2026-08-20']));

        $response = $this->actingAs($this->admin)->get(route('pegawai.kehadiran.rekap-tahunan', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee('Guru Tahunan');
        // Isi sel bulan (Hadir/hari tercatat) terpecah markup span — cek bagian "/1"
        $response->assertSee('/1');
    }
}
