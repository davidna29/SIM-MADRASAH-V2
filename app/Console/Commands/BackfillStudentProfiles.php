<?php

namespace App\Console\Commands;

use App\Models\PpdbRegistration;
use App\Models\StudentProfile;
use Illuminate\Console\Command;

class BackfillStudentProfiles extends Command
{
    protected $signature = 'ppdb:backfill-profiles';

    protected $description = 'Salin data PPDB accepted ke student_profiles (backfill untuk data lama)';

    public function handle(): int
    {
        $registrations = PpdbRegistration::where('status', 'accepted')
            ->whereNotNull('student_id')
            ->get();

        $count = 0;
        foreach ($registrations as $registration) {
            $student = $registration->student;
            if (! $student) {
                $this->warn("Registrasi {$registration->registration_no} punya student_id tapi siswa tidak ditemukan — dilewati.");

                continue;
            }

            StudentProfile::syncFromRegistration($student, $registration);
            $count++;
        }

        $this->info("Backfill selesai: {$count} profil siswa dibuat/diperbarui.");

        return self::SUCCESS;
    }
}
