<?php

namespace App\Policies;

use App\Models\User;

class OrganizationalUnitPolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function create(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function delete(User $user): bool
    {
        return $user->role === 'super_admin';
    }
}
