<?php

namespace App\Policies;

use App\Models\Letter;
use App\Models\User;

class LetterPolicy
{
    /**
     * Hanya super_admin dan tata_usaha yang bisa mengakses modul surat
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('tata_usaha');
    }

    /**
     * Detail surat bisa dilihat super_admin dan tata_usaha
     */
    public function view(User $user, Letter $letter): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('tata_usaha');
    }

    /**
     * Hanya super_admin dan tata_usaha yang bisa membuat surat
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('tata_usaha');
    }

    /**
     * Hanya super_admin dan tata_usaha yang bisa mengedit surat
     */
    public function update(User $user, Letter $letter): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('tata_usaha');
    }

    /**
     * Hanya super_admin dan tata_usaha yang bisa menghapus surat
     */
    public function delete(User $user, Letter $letter): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('tata_usaha');
    }

    /**
     * Hanya super_admin yang bisa mendisposisi surat
     */
    public function disposition(User $user, Letter $letter): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Hanya super_admin dan tata_usaha yang bisa mengelola kategori
     */
    public function manageCategory(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('tata_usaha');
    }
}
