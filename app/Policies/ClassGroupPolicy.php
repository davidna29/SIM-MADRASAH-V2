<?php

namespace App\Policies;

use App\Models\ClassGroup;
use App\Models\User;

class ClassGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah'], true);
    }

    public function view(User $user, ClassGroup $classGroup): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function update(User $user, ClassGroup $classGroup): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function delete(User $user, ClassGroup $classGroup): bool
    {
        return $user->role === 'super_admin';
    }
}
