<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\TeacherAssignment;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();
        $assignments = TeacherAssignment::with(['subject', 'classGroup'])->where('academic_year_id', $tahun->id)->get();

        $templates = [
            ['day' => 'senin', 'start' => '07:00', 'end' => '08:00'],
            ['day' => 'senin', 'start' => '08:00', 'end' => '09:00'],
            ['day' => 'selasa', 'start' => '07:00', 'end' => '08:00'],
            ['day' => 'selasa', 'start' => '09:00', 'end' => '10:00'],
            ['day' => 'rabu', 'start' => '07:00', 'end' => '08:00'],
            ['day' => 'kamis', 'start' => '08:00', 'end' => '09:00'],
            ['day' => 'jumat', 'start' => '07:00', 'end' => '08:00'],
        ];

        foreach ($assignments as $i => $assignment) {
            $t = $templates[$i % count($templates)];

            Schedule::firstOrCreate(
                [
                    'academic_year_id' => $tahun->id,
                    'teacher_assignment_id' => $assignment->id,
                    'day' => $t['day'],
                    'start_time' => $t['start'],
                ],
                [
                    'end_time' => $t['end'],
                    'room' => 'Ruang '.($i % 4 + 1),
                    'recorded_by' => auth()->id() ?: null,
                ]
            );
        }
    }
}
