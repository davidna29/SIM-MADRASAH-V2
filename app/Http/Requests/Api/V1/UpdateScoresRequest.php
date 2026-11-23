<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_assignment_id' => ['required', 'integer', 'exists:teacher_assignments,id'],
            'dry_run' => ['nullable', 'boolean'],
            'values' => ['required_without:scores', 'array'],
            'values.*' => ['array'],
            'values.*.*' => ['nullable', 'integer', 'between:0,100'],
            'scores' => ['required_without:values', 'array'],
            'scores.*' => ['nullable', 'integer', 'between:0,100'],
        ];
    }
}
