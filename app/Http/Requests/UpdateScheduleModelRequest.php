<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScheduleModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $model = $this->route('model');

        return [
            'name' => ['required', 'string', 'max:80'],
            'start_time' => ['required', 'date_format:H:i'],
            'max_hours_per_day' => ['required', 'integer', 'between:1,12'],
            'is_active' => ['sometimes', 'boolean'],
            'grade_levels' => ['required', 'array', 'min:1'],
            'grade_levels.*' => ['required', Rule::in(['I', 'II', 'III', 'IV', 'V', 'VI'])],
        ];
    }

    public function attributes(): array
    {
        return (new StoreScheduleModelRequest)->attributes();
    }
}
