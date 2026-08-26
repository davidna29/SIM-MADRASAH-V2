<?php

namespace App\Policies;

use App\Models\LibraryCategory;
use App\Models\User;

class LibraryCategoryPolicy
{
    protected array $viewerRoles = ['super_admin', 'pustakawan', 'kepala_madrasah'];

    protected array $adminRoles = ['super_admin', 'pustakawan'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    public function update(User $user, LibraryCategory $category): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    public function delete(User $user, LibraryCategory $category): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
