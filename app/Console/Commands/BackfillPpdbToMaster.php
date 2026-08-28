<?php

namespace App\Console\Commands;

use App\Models\PpdbRegistration;
use App\Support\PpdbService;
use Illuminate\Console\Command;

class BackfillPpdbToMaster extends Command
{
    protected $signature = 'ppdb:backfill-master';

    protected $description = 'Salin data PPDB accepted ke master (people/students/guardians) untuk data lama';

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

            PpdbService::syncFromRegistration($registration, $student);
            $count++;
        }

        $this->info("Backfill selesai: {$count} siswa disinkronkan ke master.");

        return self::SUCCESS;
    }
}
