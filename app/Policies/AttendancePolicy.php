<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru'], true);
    }
}
