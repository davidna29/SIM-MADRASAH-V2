<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Attendance::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date'],
            'class_group_id' => ['required', 'integer', 'exists:class_groups,id'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.status' => ['nullable', 'in:hadir,izin,sakit,alpha'],
            'attendances.*.note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
