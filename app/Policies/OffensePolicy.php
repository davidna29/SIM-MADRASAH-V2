<?php

namespace App\Policies;

use App\Models\Offense;
use App\Models\User;

class OffensePolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru_bk', 'kepala_madrasah'];

    protected array $writerRoles = ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru_bk'];

    protected array $adminRoles = ['super_admin', 'wakamad_kesiswaan'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, Offense $offense): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function update(User $user, Offense $offense): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function delete(User $user, Offense $offense): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
