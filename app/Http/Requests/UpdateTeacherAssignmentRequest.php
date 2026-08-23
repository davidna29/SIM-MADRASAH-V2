<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'guru')],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreTeacherAssignmentRequest)->attributes();
    }
}
