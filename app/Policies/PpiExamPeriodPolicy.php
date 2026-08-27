<?php

namespace App\Policies;

use App\Models\PpiExamPeriod;
use App\Models\User;

class PpiExamPeriodPolicy
{
    protected array $adminRoles = ['super_admin', 'wakamad_kurikulum'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, array_merge($this->adminRoles, ['kepala_madrasah', 'guru']), true);
    }

    public function view(User $user, PpiExamPeriod $period): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    /**
     * Setup & konfigurasi periode (skala, bobot, aspek, materi, ruang, grup, peserta).
     * Periode yang terkunci ditolak di service (assertConfigEditable).
     */
    public function manage(User $user, PpiExamPeriod $period): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    /**
     * Input nilai (setoran & ujian lisan) oleh guru penugasan / panitia.
     */
    public function input(User $user, PpiExamPeriod $period): bool
    {
        return in_array($user->role, array_merge($this->adminRoles, ['guru']), true);
    }

    public function rekapView(User $user, PpiExamPeriod $period): bool
    {
        return in_array($user->role, array_merge($this->adminRoles, ['kepala_madrasah']), true);
    }

    /**
     * Koreksi nilai di Rekap Kelas VI (dengan alasan + audit log).
     */
    public function rekapEdit(User $user, PpiExamPeriod $period): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    public function archive(User $user, PpiExamPeriod $period): bool
    {
        return in_array($user->role, $this->adminRoles, true);
    }

    /**
     * Buka/kunci konfigurasi periode yang sedang berlangsung — Super Admin saja.
     */
    public function unlock(User $user, PpiExamPeriod $period): bool
    {
        return $user->role === 'super_admin';
    }
}
