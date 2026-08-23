<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'max_hours_per_day' => ['required', 'integer', 'between:1,12'],
            'is_active' => ['sometimes', 'boolean'],
            'grade_levels' => ['required', 'array', 'min:1'],
            'grade_levels.*' => ['required', Rule::in(['I', 'II', 'III', 'IV', 'V', 'VI'])],
            'slot_duration' => ['required', 'integer', 'between:15,120'], // menit per jam ke-
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama model',
            'academic_year_id' => 'tahun ajaran',
            'start_time' => 'jam mulai',
            'max_hours_per_day' => 'maksimal jam per hari',
            'grade_levels' => 'tingkatan',
            'slot_duration' => 'durasi per jam',
        ];
    }
}
