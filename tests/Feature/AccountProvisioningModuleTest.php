<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentMutation;
use App\Models\User;
use App\Support\AccountProvisioning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountProvisioningModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin', 'username' => 'admin']);
        $this->year = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
    }

    protected function nikFrom(?string $nis): string
    {
        $seed = $nis ?? (string) random_int(100000, 999999999);

        return '3518'.str_pad(substr((string) $seed, -12), 12, '0', STR_PAD_LEFT);
    }

    protected function makeStudent(?string $nis = '251101', ?string $nisn = '0012345678', ?string $birthDate = '2016-05-10'): Student
    {
        $person = Person::create([
            'nik' => $this->nikFrom($nis ?? '999999'),
            'name' => 'Budi Pindah',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_date' => $birthDate,
        ]);

        $student = Student::create([
            'person_id' => $person->id,
            'nis' => $nis,
            'nisn' => $nisn,
            'name' => 'Budi Pindah',
            'gender' => 'L',
        ]);

        StudentEnrollment::create([
            'academic_year_id' => $this->year->id,
            'class_group_id' => ClassGroup::first()->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        return $student;
    }

    protected function makeEmployee(?string $nip = '199001152019031001', string $status = 'aktif', ?string $nik = null): Employee
    {
        $person = Person::create([
            'nik' => $nik ?? $this->nikFrom('888001'),
            'name' => 'Guru Uji',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_date' => '1990-01-15',
            'email' => 'guru.uji@madrasah.sch.id',
        ]);

        return Employee::create([
            'person_id' => $person->id,
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'nip' => $nip,
            'employee_status' => 'pns',
            'status' => $status,
        ]);
    }

    public function test_employee_store_creates_account_automatically(): void
    {
        $this->actingAs($this->admin)->post(route('pegawai.store'), [
            'name' => 'Guru Otomatis',
            'nik' => $this->nikFrom('888002'),
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_date' => '1990-01-15',
            'email' => 'guru.oto@madrasah.sch.id',
            'nip' => '199001152019031006',
            'employee_status' => 'pns',
            'status' => 'aktif',
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
        ])->assertRedirect();

        $employee = Employee::where('nip', '199001152019031006')->first();
        $this->assertNotNull($employee->user_id);
        $user = $employee->user;
        $this->assertSame('199001152019031006', $user->username);
        $this->assertSame('guru', $user->role);
        $this->assertTrue((bool) $user->must_change_password);
        $this->assertTrue((bool) $user->is_active);
        $this->assertSame('nip', $employee->fresh()->username_source);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'account_provisioning']);
    }

    public function test_employee_store_nonaktif_does_not_create_account(): void
    {
        $this->actingAs($this->admin)->post(route('pegawai.store'), [
            'name' => 'Guru Nonaktif',
            'nik' => $this->nikFrom('888003'),
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_date' => '1990-01-15',
            'nip' => '199001152019031007',
            'employee_status' => 'pns',
            'status' => 'nonaktif',
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
        ])->assertRedirect();

        $employee = Employee::where('nip', '199001152019031007')->first();
        $this->assertNull($employee->user_id);
        $this->assertSame(1, User::count()); // hanya admin
    }

    public function test_employee_uses_nik_as_username_when_nip_empty(): void
    {
        $employee = $this->makeEmployee(nip: null);
        $payload = AccountProvisioning::employeeAccountPayload($employee);

        $this->assertTrue($payload['ok']);
        $this->assertSame('nik', $payload['source']);
        $this->assertSame($employee->person->nik, $payload['payload']['username']);
    }

    public function test_employee_observer_deactivates_account_on_nonaktif_and_cuti_stays_active(): void
    {
        $employee = $this->makeEmployee();
        $user = User::factory()->create(['role' => 'guru', 'must_change_password' => false]);
        $employee->update(['user_id' => $user->id]);

        // nonaktif -> akun mati
        $employee->update(['status' => 'nonaktif']);
        $this->assertFalse((bool) $user->fresh()->is_active);

        // cuti -> tetap aktif
        $employee->update(['status' => 'cuti']);
        $this->assertTrue((bool) $user->fresh()->is_active);

        // balik aktif -> aktif lagi
        $employee->update(['status' => 'aktif']);
        $this->assertTrue((bool) $user->fresh()->is_active);
    }

    public function test_mutasi_keluar_deactivates_student_account_and_undo_reactivates(): void
    {
        $student = $this->makeStudent();
        $user = User::factory()->create(['role' => 'siswa', 'student_id' => $student->id, 'must_change_password' => false]);

        $this->actingAs($this->admin)->post(route('mutasi-keluar.store'), [
            'student_id' => $student->id,
            'tanggal_mutasi' => '2026-08-28',
            'sekolah_tujuan' => 'MTs Negeri 2',
            'alasan_pindah' => 'pindah_ortu',
        ])->assertRedirect();

        $this->assertSame('keluar', StudentEnrollment::where('student_id', $student->id)->first()->status);
        $this->assertFalse((bool) $user->fresh()->is_active);

        $mutation = StudentMutation::first();
        $this->actingAs($this->admin)->delete(route('mutasi-keluar.destroy', $mutation))->assertRedirect();

        $this->assertSame('aktif', StudentEnrollment::where('student_id', $student->id)->first()->status);
        $this->assertTrue((bool) $user->fresh()->is_active);
    }

    public function test_past_year_enrollment_change_does_not_deactivate_account(): void
    {
        $student = $this->makeStudent();
        $user = User::factory()->create(['role' => 'siswa', 'student_id' => $student->id, 'must_change_password' => false]);

        $pastYear = AcademicYear::create(['name' => '2025/2026', 'semester' => 'ganjil', 'is_active' => false]);
        $pastEnrollment = StudentEnrollment::create([
            'academic_year_id' => $pastYear->id,
            'class_group_id' => ClassGroup::first()->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $pastEnrollment->update(['status' => 'keluar']);

        $this->assertTrue((bool) $user->fresh()->is_active);
    }

    public function test_activation_index_lists_unprovisioned_students(): void
    {
        $this->makeStudent();
        $this->makeStudent('251102', '0012345679');
        $this->makeStudent(null, null, null); // data tidak lengkap (NISN & NIS kosong)

        $response = $this->actingAs($this->admin)->get(route('pengguna.aktivasi.index'));

        $response->assertOk();
        $response->assertSee('Budi Pindah');
        $response->assertSee('0012345678');
        $response->assertSee('Data Tidak Lengkap');
    }

    public function test_bulk_activation_creates_users_and_is_idempotent(): void
    {
        $student = $this->makeStudent();

        $this->actingAs($this->admin)->post(route('pengguna.aktivasi.aktifkan'), ['student_ids' => [$student->id]])
            ->assertRedirect();

        $user = User::where('student_id', $student->id)->first();
        $this->assertNotNull($user);
        $this->assertSame('0012345678', $user->username);
        $this->assertSame('siswa', $user->role);
        $this->assertTrue((bool) $user->must_change_password);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'account_provisioning']);

        // Idempotent: jalankan lagi -> tetap satu akun
        $this->actingAs($this->admin)->post(route('pengguna.aktivasi.aktifkan'), ['student_ids' => [$student->id]]);
        $this->assertSame(1, User::where('student_id', $student->id)->count());
    }

    public function test_bulk_activation_reports_incomplete_and_missing_birth_date(): void
    {
        $incomplete = $this->makeStudent(null, null, null); // NISN & NIS kosong
        $noBirth = $this->makeStudent('251105', '0012345681', null); // tanpa tanggal lahir

        $this->actingAs($this->admin)->post(route('pengguna.aktivasi.aktifkan'), ['student_ids' => [$incomplete->id, $noBirth->id]])
            ->assertRedirect();

        $this->assertNull(User::where('student_id', $incomplete->id)->first());
        $this->assertNull(User::where('student_id', $noBirth->id)->first());

        $this->get(route('pengguna.aktivasi.index'))->assertSee('NISN dan NIS kosong');
    }

    public function test_activation_export_csv_clears_credentials(): void
    {
        $student = $this->makeStudent();
        $this->actingAs($this->admin)->post(route('pengguna.aktivasi.aktifkan'), ['student_ids' => [$student->id]]);

        $response = $this->actingAs($this->admin)->get(route('pengguna.aktivasi.export'));

        $response->assertOk();
        $this->assertStringContainsString('Username', $response->streamedContent());
        $this->assertStringContainsString('0012345678', $response->streamedContent());

        // Setelah unduh, data kredensial dibuang — unduh kedua ditolak
        $this->actingAs($this->admin)->get(route('pengguna.aktivasi.export'))->assertSessionHasErrors('export');
    }

    public function test_backfill_command_links_users_without_creating_new_ones(): void
    {
        // Pegawai: cocok via email person
        $person = Person::create([
            'nik' => $this->nikFrom('888010'),
            'name' => 'Guru Lama',
            'gender' => 'L',
            'religion' => 'Islam',
            'email' => 'guru.lama@madrasah.sch.id',
        ]);
        $employee = Employee::create([
            'person_id' => $person->id,
            'position_id' => Position::first()->id,
            'organizational_unit_id' => OrganizationalUnit::first()->id,
            'nip' => null,
            'employee_status' => 'honor',
            'status' => 'aktif',
        ]);
        $userGuru = User::factory()->create(['role' => 'guru', 'email' => 'guru.lama@madrasah.sch.id']);

        // Siswa: cocok via pola username siswa.<token>
        $student = $this->makeStudent('251106');
        $userSiswa = User::factory()->create(['role' => 'siswa', 'username' => 'siswa.budi']);

        $totalUsers = User::count();

        $this->artisan('account:backfill-links')->assertExitCode(0);

        $this->assertSame($totalUsers, User::count()); // tidak membuat user baru
        $this->assertSame($userGuru->id, $employee->fresh()->user_id);
        $this->assertSame($student->id, $userSiswa->fresh()->student_id);
    }

    public function test_must_change_password_forces_redirect_to_change_page(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'must_change_password' => true]);

        $this->actingAs($user)->get(route('guru.penugasan'))->assertRedirect(route('password.change'));
        $this->actingAs($user)->get(route('password.change'))->assertOk();

        $this->actingAs($user)->post(route('password.update'), [
            'current_password' => 'password',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect();

        $this->assertFalse((bool) $user->fresh()->must_change_password);
        $this->actingAs($user->fresh())->get(route('guru.penugasan'))->assertOk();
    }

    public function test_inactive_account_cannot_login(): void
    {
        User::factory()->create([
            'role' => 'guru',
            'username' => 'guru.non',
            'email' => 'guru.non@madrasah.sch.id',
            'is_active' => false,
        ]);

        $this->post(route('login.attempt'), ['login' => 'guru.non', 'password' => 'password']);

        $this->assertGuest();
        $this->assertDatabaseMissing('sessions', ['user_id' => 1]);
    }
}
