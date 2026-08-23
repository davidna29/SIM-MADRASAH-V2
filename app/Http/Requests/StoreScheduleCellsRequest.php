<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleCellsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cells' => ['required', 'array'],
            'cells.*.class_group_id' => ['required', 'exists:class_groups,id'],
            'cells.*.day' => ['required', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])],
            'cells.*.period_no' => ['required', 'integer', 'min:1', 'max:12'],
            'cells.*.teacher_id' => ['nullable', 'exists:users,id'],
            'cells.*.subject_id' => ['nullable', 'exists:subjects,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cells' => 'sel jadwal',
            'cells.*.day' => 'hari',
            'cells.*.period_no' => 'jam ke-',
            'cells.*.teacher_id' => 'guru',
            'cells.*.subject_id' => 'mata pelajaran',
        ];
    }
}
