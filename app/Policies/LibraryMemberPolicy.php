<?php

namespace App\Policies;

use App\Models\LibraryMember;
use App\Models\User;

class LibraryMemberPolicy
{
    protected array $viewerRoles = ['super_admin', 'pustakawan', 'kepala_madrasah'];

    protected array $managerRoles = ['super_admin', 'pustakawan'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, LibraryMember $member): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }

    public function update(User $user, LibraryMember $member): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }

    public function delete(User $user, LibraryMember $member): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }
}
