<?php

namespace App\Policies;

use App\Models\TeachingJournal;
use App\Models\User;

class TeachingJournalPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'wakamad_kurikulum', 'kepala_madrasah', 'guru', 'tata_usaha'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'guru'], true);
    }

    public function update(User $user, TeachingJournal $journal): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $journal->assignment?->user_id === $user->id;
    }

    public function delete(User $user, TeachingJournal $journal): bool
    {
        return $this->update($user, $journal);
    }
}
