<?php

namespace App\Policies;

use App\Models\PembiasaanMateri;
use App\Models\User;

class PembiasaanMateriPolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru', 'kepala_madrasah'];

    protected array $writerRoles = ['super_admin', 'wakamad_kesiswaan', 'wali_kelas', 'guru'];

    protected array $adminRoles = ['super_admin', 'wakamad_kesiswaan'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, PembiasaanMateri $materi): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function update(User $user, PembiasaanMateri $materi): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function delete(User $user, PembiasaanMateri $materi): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    public function input(User $user): bool
    {
        return in_array($user->role, $this->writerRoles, true);
    }

    public function configure(User $user): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
