<?php

namespace App\Policies;

use App\Models\PublicPage;
use App\Models\User;

class PublicPagePolicy
{
    protected array $roles = ['super_admin', 'wakamad_humas', 'editor_berita', 'kepala_madrasah'];

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PublicPage $publicPage): bool
    {
        return $this->isEditor($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PublicPage $publicPage): bool
    {
        return $this->isEditor($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PublicPage $publicPage): bool
    {
        return $this->isEditor($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PublicPage $publicPage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PublicPage $publicPage): bool
    {
        return $this->isEditor($user);
    }

    protected function isEditor(User $user): bool
    {
        return in_array($user->role, $this->roles, true);
    }
}
