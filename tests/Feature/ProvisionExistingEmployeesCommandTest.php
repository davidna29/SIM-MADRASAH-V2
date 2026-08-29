<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvisionExistingEmployeesCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        Position::create(['code' => 'SATPAM', 'name' => 'Satpam']);
        OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
    }

    protected function makeEmployee(string $name, ?string $nip = '199001152019031001', ?string $birthDate = '1990-01-15', string $posCode = 'GURU_MAPEL'): Employee
    {
        $person = Person::create([
            'nik' => '3518'.str_pad((string) random_int(100000000, 999999999), 12, '0', STR_PAD_LEFT),
            'name' => $name,
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_date' => $birthDate,
        ]);

        return Employee::create([
            'person_id' => $person->id,
            'position_id' => Position::where('code', $posCode)->first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'nip' => $nip,
            'employee_status' => 'pns',
            'status' => 'aktif',
        ]);
    }

    public function test_dry_run_does_not_modify_database(): void
    {
        $this->makeEmployee('Guru Uji', '199001152019031001');
        $userCountBefore = User::count();
        $employee = Employee::first();

        $this->artisan('account:provision-existing-employees', ['--dry-run' => true])
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain('Guru Uji')
            ->expectsOutputToContain('tidak ada data yang ditulis');

        $this->assertSame($userCountBefore, User::count());
        $this->assertNull($employee->fresh()->user_id);
    }

    public function test_execution_creates_users_and_links_employees(): void
    {
        $guru = $this->makeEmployee('Guru Aktif', '199001152019031001');
        $satpam = $this->makeEmployee('Pak Satpam', null, '1988-05-20', 'SATPAM');

        $this->artisan('account:provision-existing-employees')
            ->expectsOutputToContain('berhasil: 2');

        $guruUser = $guru->fresh()->user;
        $this->assertNotNull($guruUser);
        $this->assertSame('199001152019031001', $guruUser->username);
        $this->assertSame('guru', $guruUser->role);
        $this->assertSame('nip', $guru->fresh()->username_source);

        $satpamUser = $satpam->fresh()->user;
        $this->assertNotNull($satpamUser);
        $this->assertSame('tata_usaha', $satpamUser->role);
        $this->assertSame('nik', $satpam->fresh()->username_source);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'account_provisioning',
            'description' => 'Akun pegawai dibuat otomatis: 199001152019031001',
        ]);
    }

    public function test_execution_is_idempotent(): void
    {
        $this->makeEmployee('Guru Uji', '199001152019031001');

        $this->artisan('account:provision-existing-employees')
            ->expectsOutputToContain('berhasil: 1');

        $this->assertSame(1, User::where('username', '199001152019031001')->count());

        // Run again — should skip the already-linked employee
        $this->artisan('account:provision-existing-employees')
            ->expectsOutputToContain('Tidak ada pegawai aktif yang perlu diproses');

        $this->assertSame(1, User::where('username', '199001152019031001')->count());
    }

    public function test_failed_employee_does_not_break_others(): void
    {
        $ok = $this->makeEmployee('Guru Baik', '199001152019031001', '1990-01-15');
        $fail = $this->makeEmployee('Guru Tanpa TTL', '199001152019031002', null);
        $fail->person->update(['birth_date' => null]);
        $fail->refresh();

        $this->artisan('account:provision-existing-employees')
            ->expectsOutputToContain('berhasil: 1');

        $this->assertNotNull($ok->fresh()->user_id);
        $this->assertNull($fail->fresh()->user_id);
    }

    public function test_previously_linked_employees_are_skipped(): void
    {
        $employee = $this->makeEmployee('Sudah Terhubung', '199001152019031001');
        $user = User::factory()->create(['role' => 'guru']);
        $employee->update(['user_id' => $user->id]);

        $this->artisan('account:provision-existing-employees')
            ->expectsOutputToContain('Tidak ada pegawai aktif yang perlu diproses');

        $this->assertSame($user->id, $employee->fresh()->user_id);
    }
}
