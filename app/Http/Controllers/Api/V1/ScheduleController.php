<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ScheduleCell;
use App\Models\ScheduleModel;
use App\Support\Api\ApiAcademicYear;
use App\Support\Api\ApiLookup;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private const DAYS = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kelas' => ['required', 'string', 'max:50'],
            'hari' => ['nullable', 'in:'.implode(',', self::DAYS)],
        ]);

        $year = ApiAcademicYear::active();
        $classGroup = ApiLookup::classGroup($validated['kelas']);

        $model = ScheduleModel::query()
            ->where('academic_year_id', $year->id)
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        if (! $model) {
            return ApiResponse::success([], [
                'filters' => ['kelas' => $classGroup->name, 'hari' => $validated['hari'] ?? null],
            ], ['Belum ada model jadwal untuk tahun ajaran aktif.']);
        }

        $cells = ScheduleCell::query()
            ->with(['subject:id,code,name', 'teacher:id,name', 'classGroup:id,name', 'scheduleModel.slots'])
            ->where('schedule_model_id', $model->id)
            ->where('academic_year_id', $year->id)
            ->where('class_group_id', $classGroup->id)
            ->when($validated['hari'] ?? null, fn ($query, $day) => $query->where('day', $day))
            ->orderByRaw("FIELD(day,'senin','selasa','rabu','kamis','jumat','sabtu')")
            ->orderBy('period_no')
            ->get()
            ->map(function (ScheduleCell $cell) {
                $slot = $cell->scheduleModel?->slots->firstWhere('period_no', $cell->period_no);

                return [
                    'id' => $cell->id,
                    'day' => $cell->day,
                    'period_no' => $cell->period_no,
                    'time' => $slot ? [
                        'start' => $slot->start_time?->format('H:i'),
                        'end' => $slot->end_time?->format('H:i'),
                        'is_break' => (bool) $slot->is_break,
                    ] : null,
                    'class_group' => [
                        'id' => $cell->classGroup?->id,
                        'name' => $cell->classGroup?->name,
                    ],
                    'subject' => $cell->subject ? [
                        'id' => $cell->subject->id,
                        'code' => $cell->subject->code,
                        'name' => $cell->subject->name,
                    ] : null,
                    'teacher' => $cell->teacher ? [
                        'id' => $cell->teacher->id,
                        'name' => $cell->teacher->name,
                    ] : null,
                ];
            });

        return ApiResponse::success($cells, [
            'filters' => [
                'kelas' => $classGroup->name,
                'hari' => $validated['hari'] ?? null,
            ],
            'schedule_model' => [
                'id' => $model->id,
                'name' => $model->name,
                'is_active' => (bool) $model->is_active,
            ],
        ]);
    }
}
