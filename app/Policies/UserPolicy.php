<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function view(User $user, User $target): bool
    {
        return $user->role === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user, User $target): bool
    {
        return $user->role === 'super_admin';
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }

        if ($target->role === 'super_admin') {
            $otherAdmins = User::where('id', '!=', $target->id)
                ->where('role', 'super_admin')
                ->exists();

            return $otherAdmins;
        }

        return $user->role === 'super_admin';
    }
}
