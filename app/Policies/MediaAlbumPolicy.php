<?php

namespace App\Policies;

use App\Models\MediaAlbum;
use App\Models\User;

class MediaAlbumPolicy
{
    protected array $roles = ['super_admin', 'wakamad_humas', 'editor_berita', 'kepala_madrasah', 'tata_usaha'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function view(User $user, MediaAlbum $album): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function update(User $user, MediaAlbum $album): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function delete(User $user, MediaAlbum $album): bool
    {
        return in_array($user->role, $this->roles, true);
    }
}
