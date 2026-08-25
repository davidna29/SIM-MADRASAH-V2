<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah'];

    protected array $managerRoles = ['super_admin', 'wakamad_sarpras', 'tata_usaha'];

    protected array $adminRoles = ['super_admin', 'wakamad_sarpras'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, InventoryItem $item): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }

    public function update(User $user, InventoryItem $item): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }

    public function delete(User $user, InventoryItem $item): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    /** Persetujuan mutasi hanya untuk super_admin / wakamad_sarpras. */
    public function approve(User $user): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    /** Kelola kategori hanya untuk super_admin / wakamad_sarpras. */
    public function manageCategories(User $user): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
