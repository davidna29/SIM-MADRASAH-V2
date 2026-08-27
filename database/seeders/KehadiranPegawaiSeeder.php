<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KehadiranPegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('status', 'aktif')->get();

        if ($employees->isEmpty()) {
            return;
        }

        $recorder = User::where('role', 'super_admin')->first();
        $today = Carbon::today();

        // ~2 minggu hari kerja terakhir (Senin–Sabtu, bukan Minggu)
        $dates = collect();
        for ($i = 13; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            if ($day->isSunday()) {
                continue;
            }
            $dates->push($day);
        }

        foreach ($employees as $employee) {
            foreach ($dates as $index => $date) {
                $mod = ($index + $employee->id) % 13;

                $status = match (true) {
                    $mod === 0 => 'sakit',
                    $mod === 3 => 'izin',
                    $mod === 8 => 'terlambat',
                    default => 'hadir',
                };

                $clockIn = in_array($status, ['hadir', 'terlambat'], true) ? '07:15' : null;
                if ($status === 'terlambat') {
                    $clockIn = '07:45';
                }

                EmployeeAttendance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->toDateString(),
                    ],
                    [
                        'status' => $status,
                        'clock_in' => $clockIn,
                        'clock_out' => in_array($status, ['hadir', 'terlambat'], true) ? '13:45' : null,
                        'note' => $status === 'sakit' ? 'Sakit (surat keterangan)' : null,
                        'recorded_by' => $recorder?->id,
                    ]
                );
            }
        }
    }
}
