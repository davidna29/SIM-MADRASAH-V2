<?php

namespace App\Observers;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\User;

class StudentEnrollmentObserver
{
    /**
     * Deaktivasi/aktivasi akun user siswa mengikuti status enrollment TAHUN BERJALAN.
     * Enrollment tahun lain tidak memicu apa pun (siswa punya banyak baris lintas tahun).
     * Status terminal (config ...student_enrollment_terminal_statuses:
     * 'keluar'/'alumni') mematikan akun; balik ke 'aktif' mengaktifkannya lagi.
     */
    public function updated(StudentEnrollment $enrollment): void
    {
        if (! $enrollment->wasChanged('status')) {
            return;
        }

        $tahun = AcademicYear::where('is_active', true)->first();

        // Hanya enrollment tahun ajaran berjalan yang dianggap.
        if (! $tahun || $enrollment->academic_year_id !== $tahun->id) {
            return;
        }

        $user = User::where('student_id', $enrollment->student_id)->first();

        if (! $user) {
            return;
        }

        $terminal = config('account_provisioning.student_enrollment_terminal_statuses', ['keluar', 'alumni']);

        $user->update(['is_active' => ! in_array($enrollment->status, $terminal, true)]);
    }
}
