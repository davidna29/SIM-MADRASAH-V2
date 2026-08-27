<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    protected array $viewerRoles = ['super_admin', 'wakamad_sarpras', 'tata_usaha', 'kepala_madrasah'];

    protected array $managerRoles = ['super_admin', 'wakamad_sarpras', 'tata_usaha'];

    protected array $adminRoles = ['super_admin', 'wakamad_sarpras'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function view(User $user, Room $room): bool
    {
        return in_array($user->role, $this->viewerRoles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }

    public function update(User $user, Room $room): bool
    {
        return in_array($user->role, $this->managerRoles, true);
    }

    public function delete(User $user, Room $room): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }
}
