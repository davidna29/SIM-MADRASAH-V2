<?php

namespace App\Policies;

use App\Models\Extracurricular;
use App\Models\User;

class ExtracurricularPolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_kesiswaan', 'guru', 'wali_kelas', 'guru_bk', 'kepala_madrasah'];

    protected array $adminRoles = ['super_admin', 'wakamad_kesiswaan'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, Extracurricular $ekskul): bool
    {
        return in_array($user->role, $this->viewerRoles, true)
            || $ekskul->pembina_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    /** Kelola isi (anggota & presensi): admin kesiswaan atau pembina ekskul terkait. */
    public function update(User $user, Extracurricular $ekskul): bool
    {
        if (in_array($user->role, $this->adminRoles, true)) {
            return true;
        }

        return $ekskul->pembina_id === $user->id;
    }

    public function delete(User $user, Extracurricular $ekskul): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
