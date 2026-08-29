<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Person;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;

class AccountBackfillLinks extends Command
{
    protected $signature = 'account:backfill-links';

    protected $description = 'Cocokkan user lama yang belum ter-link ke students/employees (tanpa membuat user baru)';

    public function handle(): int
    {
        $matchedStudents = 0;
        $matchedEmployees = 0;
        $unmatched = [];

        // 1) User -> siswa: hanya yang belum punya student_id.
        $studentUsers = User::query()
            ->whereNull('student_id')
            ->where(fn ($q) => $q->where('role', 'siswa')->orWhere('username', 'like', 'siswa.%'))
            ->get();

        foreach ($studentUsers as $user) {
            $student = $this->matchStudent($user);

            if (! $student) {
                $unmatched[] = "siswa: {$user->username} ({$user->email})";

                continue;
            }

            $user->update(['student_id' => $student->id]);

            activity('account_provisioning')
                ->performedOn($student)
                ->log('Backfill: akun '.$user->username.' ditautkan ke siswa '.$student->displayName());

            $matchedStudents++;
        }

        // 2) User -> pegawai: role yang berkorespondensi ke karyawan & belum ter-link.
        $linkedUserIds = Employee::whereNotNull('user_id')->pluck('user_id')->all();

        $employeeUsers = User::query()
            ->whereNotIn('id', $linkedUserIds)
            ->where(function ($q) {
                $q->whereIn('role', [
                    'guru', 'guru_bk', 'kepala_madrasah',
                    'wakamad_kurikulum', 'wakamad_kesiswaan', 'wakamad_sarpras', 'wakamad_humas',
                    'bendahara', 'tata_usaha', 'pustakawan',
                ])->orWhere('username', 'like', 'guru.%');
            })
            ->get();

        foreach ($employeeUsers as $user) {
            $employee = $this->matchEmployee($user);

            if (! $employee) {
                $unmatched[] = "pegawai: {$user->username} ({$user->email})";

                continue;
            }

            $employee->update(['user_id' => $user->id]);

            activity('account_provisioning')
                ->performedOn($employee)
                ->log('Backfill: akun '.$user->username.' ditautkan ke pegawai '.$employee->person->name);

            $matchedEmployees++;
        }

        $this->info('Backfill selesai.');
        $this->info("User ditautkan ke siswa   : {$matchedStudents}");
        $this->info("User ditautkan ke pegawai : {$matchedEmployees}");

        if ($unmatched === []) {
            $this->info('Semua user berhasil dicocokkan.');

            return self::SUCCESS;
        }

        $this->warn('Belum bisa dicocokkan (perlu review manual — tidak dipaksa link):');
        foreach ($unmatched as $item) {
            $this->line("  - {$item}");
        }

        return self::SUCCESS;
    }

    protected function matchStudent(User $user): ?Student
    {
        // Strategi 1: email sama dengan people.email milik siswa.
        $person = $user->email ? Person::where('email', $user->email)->first() : null;

        if ($person) {
            $student = Student::where('person_id', $person->id)->first();

            if ($student) {
                return $student;
            }
        }

        // Strategi 2: pola username siswa.<token> cocok tepat satu nama.
        $token = $this->usernameToken($user, ['siswa.']);

        if ($token) {
            $candidates = Student::with('person')->get()
                ->filter(fn ($s) => str_contains(mb_strtolower($s->displayName()), $token));

            if ($candidates->count() === 1) {
                return $candidates->first();
            }
        }

        return null;
    }

    protected function matchEmployee(User $user): ?Employee
    {
        // Strategi 1: email sama dengan people.email milik pegawai.
        $person = $user->email ? Person::where('email', $user->email)->first() : null;

        if ($person) {
            $employee = Employee::where('person_id', $person->id)->whereNull('user_id')->first();

            if ($employee) {
                return $employee;
            }
        }

        // Strategi 2: pola username guru.<token> (dan sejenis) cocok tepat satu nama pegawai.
        $token = $this->usernameToken($user, [
            'guru.', 'guru_bk.', 'kepala.', 'bendahara.', 'tu.', 'pustakawan.', 'wakamad.',
        ]);

        if ($token) {
            $candidates = Employee::with('person')->whereNull('user_id')->get()
                ->filter(fn ($e) => str_contains(mb_strtolower((string) $e->person?->name), $token));

            if ($candidates->count() === 1) {
                return $candidates->first();
            }
        }

        return null;
    }

    protected function usernameToken(User $user, array $prefixes): ?string
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with((string) $user->username, $prefix)) {
                $token = strtolower(substr((string) $user->username, strlen($prefix)));

                return strlen($token) >= 2 ? $token : null;
            }
        }

        return null;
    }
}
