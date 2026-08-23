<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\ScheduleCell;
use App\Models\ScheduleModel;
use App\Models\ScheduleModelGradeLevel;
use App\Models\ScheduleSlot;
use App\Models\Subject;
use App\Models\User;
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

        // Isi contoh sel master untuk beberapa hari/rombel agar tabel penyusunan hidup
        $this->seedExampleCells($tahun);
    }

    protected function seedExampleCells(AcademicYear $tahun): void
    {
        $teachers = User::where('role', 'guru')->orderBy('id')->get();
        $subjects = Subject::orderBy('id')->get();

        if ($teachers->count() < 3 || $subjects->count() < 3) {
            return;
        }

        $mat = $subjects->firstWhere('code', 'MAT');
        $bin = $subjects->firstWhere('code', 'BIN');
        $ipa = $subjects->firstWhere('code', 'IPA');
        $pai = $subjects->firstWhere('code', 'PAI');

        $models = ScheduleModel::with('gradeLevelRows')->where('academic_year_id', $tahun->id)->get();

        foreach ($models as $model) {
            $levels = $model->gradeLevels();
            $rombel = ClassGroup::whereIn('grade_level', $levels)->orderBy('name')->get();
            if ($rombel->isEmpty()) {
                continue;
            }

            $first = $rombel->first();
            $second = $rombel->skip(1)->first();
            $days = ['senin', 'selasa', 'rabu'];
            $assign = [
                [$mat, $teachers[0]],
                [$bin, $teachers[1]],
                [$ipa, $teachers[2]],
            ];

            foreach ($days as $di => $day) {
                foreach ([1, 2] as $period) {
                    if (! $first) {
                        break;
                    }
                    [$subject, $teacher] = $assign[$period - 1];

                    ScheduleCell::firstOrCreate(
                        [
                            'schedule_model_id' => $model->id,
                            'academic_year_id' => $tahun->id,
                            'class_group_id' => $first->id,
                            'day' => $day,
                            'period_no' => $period,
                        ],
                        [
                            'teacher_id' => $teacher->id,
                            'subject_id' => $subject->id,
                        ]
                    );

                    if ($second && $period === 1) {
                        ScheduleCell::firstOrCreate(
                            [
                                'schedule_model_id' => $model->id,
                                'academic_year_id' => $tahun->id,
                                'class_group_id' => $second->id,
                                'day' => $day,
                                'period_no' => $period,
                            ],
                            [
                                'teacher_id' => $teacher->id,
                                'subject_id' => $subject->id,
                            ]
                        );
                    }
                }
            }
        }
    }
}
