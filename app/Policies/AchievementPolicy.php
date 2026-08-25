<?php

namespace App\Policies;

use App\Models\Achievement;
use App\Models\User;

class AchievementPolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah'];

    protected array $writerRoles = ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru'];

    protected array $adminRoles = ['super_admin', 'wakamad_kesiswaan'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, Achievement $achievement): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function update(User $user, Achievement $achievement): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function delete(User $user, Achievement $achievement): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
