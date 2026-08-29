<?php

namespace App\Observers;

use App\Models\Employee;

class EmployeeObserver
{
    /**
     * Deaktivasi/aktivasi akun user pegawai mengikuti perubahan status pegawai.
     * Status terminal (config account_provisioning.employee_terminal_statuses,
     * yaitu 'nonaktif') mematikan akun; 'cuti' TIDAK mematikannya.
     */
    public function updated(Employee $employee): void
    {
        if (! $employee->wasChanged('status')) {
            return;
        }

        $user = $employee->user;

        if (! $user) {
            return;
        }

        $terminal = config('account_provisioning.employee_terminal_statuses', ['nonaktif']);

        $user->update(['is_active' => ! in_array($employee->status, $terminal, true)]);
    }
}
