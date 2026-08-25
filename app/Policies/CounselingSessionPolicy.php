<?php

namespace App\Policies;

use App\Models\CounselingSession;
use App\Models\User;

class CounselingSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'guru_bk', 'wakamad_kesiswaan', 'kepala_madrasah', 'wali_kelas'], true);
    }

    public function view(User $user, CounselingSession $session): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'guru_bk' && $session->counselor_user_id === $user->id) {
            return true;
        }

        if ($user->role === 'kepala_madrasah' && in_array($session->confidentiality_level, ['plus_kepala', 'plus_wali_kelas'], true)) {
            return true;
        }

        if ($user->role === 'wali_kelas' && $session->confidentiality_level === 'plus_wali_kelas') {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'guru_bk'], true);
    }

    public function update(User $user, CounselingSession $session): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'guru_bk' && $session->counselor_user_id === $user->id;
    }

    public function delete(User $user, CounselingSession $session): bool
    {
        return $user->role === 'super_admin';
    }
}
