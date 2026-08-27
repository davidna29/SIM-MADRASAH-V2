<?php

namespace App\Http\Requests;

use App\Models\EmployeeAttendance;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statuses = implode(',', array_keys(EmployeeAttendance::STATUSES));

        return [
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['nullable', "in:{$statuses}"],
            'attendances.*.clock_in' => ['nullable', 'date_format:H:i'],
            'attendances.*.clock_out' => ['nullable', 'date_format:H:i'],
            'attendances.*.note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['attendance_date' => 'tanggal'];
    }
}
