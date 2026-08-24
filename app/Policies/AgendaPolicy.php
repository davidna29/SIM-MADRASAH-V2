<?php

namespace App\Policies;

use App\Models\Agenda;
use App\Models\User;

class AgendaPolicy
{
    protected array $roles = ['editor_berita', 'wakamad_humas', 'tata_usaha', 'kepala_madrasah', 'super_admin'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function view(User $user, Agenda $agenda): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function update(User $user, Agenda $agenda): bool
    {
        return in_array($user->role, $this->roles, true);
    }

    public function delete(User $user, Agenda $agenda): bool
    {
        return in_array($user->role, $this->roles, true);
    }
}
