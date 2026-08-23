<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function update(User $user, Subject $subject): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->role === 'super_admin';
    }
}
