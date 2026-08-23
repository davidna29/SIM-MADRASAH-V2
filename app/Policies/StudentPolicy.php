<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kesiswaan', 'tata_usaha', 'wali_kelas', 'kepala_madrasah'], true);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kesiswaan', 'tata_usaha'], true);
    }

    public function update(User $user, Student $student): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kesiswaan', 'tata_usaha'], true);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->role === 'super_admin';
    }
}
