<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('classGroup');

        return [
            'name' => ['required', 'string', 'max:20', 'unique:class_groups,name,'.$id?->id],
            'grade_level' => ['required', 'in:I,II,III,IV,V,VI'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreClassGroupRequest)->attributes();
    }
}
