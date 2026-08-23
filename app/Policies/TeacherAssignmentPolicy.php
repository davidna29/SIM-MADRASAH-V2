<?php

namespace App\Policies;

use App\Models\TeacherAssignment;
use App\Models\User;

class TeacherAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function update(User $user, TeacherAssignment $assignment): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum'], true);
    }

    public function delete(User $user, TeacherAssignment $assignment): bool
    {
        return $user->role === 'super_admin';
    }
}
