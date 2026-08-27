<?php

namespace App\Policies;

use App\Models\User;

class EmployeeAttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'tata_usaha', 'kepala_madrasah'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'tata_usaha'], true);
    }
}
