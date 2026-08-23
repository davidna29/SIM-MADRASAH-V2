<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20', 'unique:class_groups,name'],
            'grade_level' => ['required', 'in:VII,VIII,IX'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama kelas',
            'grade_level' => 'tingkat',
        ];
    }
}
