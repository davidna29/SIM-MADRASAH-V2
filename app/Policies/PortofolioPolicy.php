<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\User;

class PortofolioPolicy
{
    /**
     * Siapa yang bisa melihat daftar portofolio.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('wakamad_kesiswaan') || $user->hasRole('wali_kelas') || $user->hasRole('guru_bk') || $user->hasRole('kepala_madrasah');
    }

    /**
     * Detail portofolio — super_admin, wakamad kesiswaan, kepala madrasah: semua siswa.
     * Wali kelas: hanya siswa di rombelnya. Guru BK: semua siswa.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('wakamad_kesiswaan') || $user->hasRole('guru_bk') || $user->hasRole('kepala_madrasah')) {
            return true;
        }

        if ($user->hasRole('wali_kelas')) {
            $tahun = AcademicYear::active();

            return $student->enrollments()
                ->where('academic_year_id', $tahun?->id)
                ->whereHas('classGroup.homeroom', fn ($q) => $q->where('user_id', $user->id))
                ->exists();
        }

        return false;
    }
}
