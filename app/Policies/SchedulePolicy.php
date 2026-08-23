<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum', 'guru', 'kepala_madrasah'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->role === 'super_admin';
    }
}
