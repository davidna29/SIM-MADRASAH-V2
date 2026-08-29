<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use App\Support\AccountProvisioning;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProvisionExistingEmployees extends Command
{
    protected $signature = 'account:provision-existing-employees {--dry-run}';

    protected $description = 'Provision akun user untuk pegawai aktif yang belum punya akun (idempotent, aman dijalankan berulang)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $employees = Employee::query()
            ->where('status', 'aktif')
            ->whereNull('user_id')
            ->with(['person', 'position'])
            ->orderBy('id')
            ->get();

        if ($employees->isEmpty()) {
            $this->info('Tidak ada pegawai aktif yang perlu diproses. Semua sudah terhubung ke akun.');

            return self::SUCCESS;
        }

        $this->info(($dryRun ? 'DRY RUN — ' : '').$employees->count().' pegawai aktif belum punya akun akan diproses.');
        $this->newLine();

        // Header tabel
        $this->table(
            ['#', 'Nama', 'Posisi', 'Username', 'Role', 'Masalah'],
            $employees->map(function (Employee $employee) {
                $payload = AccountProvisioning::employeeAccountPayload($employee);
                $pos = $employee->position?->code ?? '—';
                $posName = $employee->position?->name ?? '—';

                if (! $payload['ok']) {
                    return [$employee->id, $employee->person?->name ?? '—', $posName, '—', '—', $payload['reason']];
                }

                // Cek collision (username sudah dipakai)
                $base = $payload['payload']['username'];
                $final = AccountProvisioning::uniqueUsername($base, 'p');
                $collision = ($final !== $base && $final !== null) ? "collision: {$final}" : '';

                return [
                    $employee->id,
                    $employee->person?->name ?? '—',
                    $posName,
                    $final ?? '—',
                    $payload['payload']['role'],
                    $collision ?: '',
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Ini dry run — tidak ada data yang ditulis ke database.');
            $this->info('Jalankan tanpa --dry-run untuk mengeksekusi.');

            return self::SUCCESS;
        }

        $created = 0;
        $failed = [];
        $skipped = 0;

        foreach ($employees as $employee) {
            $result = $this->provisionOne($employee);

            if ($result === true) {
                $created++;
            } elseif ($result === 'skip') {
                $skipped++;
            } else {
                $failed[] = $employee->person->name.' ('.$result.')';
            }
        }

        $this->newLine();
        $this->info("Selesai — berhasil: {$created} · dilewati: {$skipped} · gagal: ".count($failed));

        if ($failed !== []) {
            $this->warn('Pegawai yang gagal (perlu perbaikan data manual):');
            foreach ($failed as $item) {
                $this->line("  - {$item}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Provision satu pegawai — transaksi terpisah per pegawai.
     * Mengembalikan true (sukses), 'skip' (sudah ada user), atau string alasan gagal.
     */
    protected function provisionOne(Employee $employee): true|string
    {
        // Guard: pegawai mungkin sudah punya user_id (race condition / proses paralel)
        if ($employee->fresh()->user_id !== null) {
            return 'skip';
        }

        $payload = AccountProvisioning::employeeAccountPayload($employee);

        if (! $payload['ok']) {
            return $payload['reason'];
        }

        return DB::transaction(function () use ($employee, $payload) {
            // Guard ulang dalam transaksi (lock logical)
            $employee->refresh();

            if ($employee->user_id !== null) {
                return 'skip';
            }

            $user = User::create($payload['payload']);

            $employee->update([
                'user_id' => $user->id,
                'username_source' => $payload['source'],
            ]);

            activity('account_provisioning')
                ->performedOn($employee)
                ->log('Akun pegawai dibuat otomatis: '.$user->username);

            return true;
        });
    }
}
