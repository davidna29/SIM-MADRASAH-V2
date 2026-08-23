<?php

namespace App\Policies;

use App\Models\ScheduleModel;
use App\Models\User;

class ScheduleModelPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum', 'guru', 'kepala_madrasah'], true);
    }

    public function view(User $user, ScheduleModel $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function update(User $user, ScheduleModel $model): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function delete(User $user, ScheduleModel $model): bool
    {
        return $user->role === 'super_admin';
    }
}
