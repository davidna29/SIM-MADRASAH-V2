<?php

namespace App\Policies;

use App\Models\TuitionPayment;
use App\Models\User;

class TuitionPaymentPolicy
{
    protected array $manageRoles = ['super_admin', 'bendahara', 'tata_usaha'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [...$this->manageRoles, 'kepala_madrasah'], true);
    }

    public function view(User $user, TuitionPayment $payment): bool
    {
        return $this->manage($user);
    }

    public function manage(User $user): bool
    {
        return in_array($user->role, $this->manageRoles, true);
    }

    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    public function update(User $user, TuitionPayment $payment): bool
    {
        return $this->manage($user);
    }

    public function delete(User $user, TuitionPayment $payment): bool
    {
        return $this->manage($user);
    }
}
