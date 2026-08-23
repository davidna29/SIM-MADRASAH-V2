<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ScheduleModel;
use App\Models\ScheduleModelGradeLevel;
use App\Models\ScheduleSlot;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();

        // Contoh model fleksibel — admin dapat mengubah/berkurang/bertambah kapan saja.
        $templates = [
            ['name' => 'Kurikulum Kelas I', 'start' => '07:00', 'max' => 6, 'duration' => 35, 'levels' => ['I']],
            ['name' => 'Kurikulum Kelas II–IV', 'start' => '07:00', 'max' => 7, 'duration' => 35, 'levels' => ['II', 'III', 'IV']],
            ['name' => 'Kurikulum Kelas V–VI', 'start' => '07:00', 'max' => 8, 'duration' => 40, 'levels' => ['V', 'VI']],
        ];

        foreach ($templates as $t) {
            $model = ScheduleModel::firstOrCreate(
                [
                    'academic_year_id' => $tahun->id,
                    'name' => $t['name'],
                ],
                [
                    'start_time' => $t['start'],
                    'max_hours_per_day' => $t['max'],
                    'is_active' => true,
                    'created_by' => null,
                ]
            );

            foreach ($t['levels'] as $level) {
                ScheduleModelGradeLevel::firstOrCreate([
                    'schedule_model_id' => $model->id,
                    'grade_level' => $level,
                ]);
            }

            if ($model->slots()->count() === 0) {
                $start = \Carbon\Carbon::parse($t['start']);
                for ($i = 1; $i <= $t['max']; $i++) {
                    ScheduleSlot::create([
                        'schedule_model_id' => $model->id,
                        'period_no' => $i,
                        'start_time' => $start->format('H:i'),
                        'end_time' => $start->copy()->addMinutes($t['duration'])->format('H:i'),
                        'is_break' => false,
                    ]);
                    $start->addMinutes($t['duration']);
                }
            }
        }
    }
}
