<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('subject');

        return [
            'code' => ['required', 'string', 'max:10', 'unique:subjects,code,'.$id?->id],
            'name' => ['required', 'string', 'max:60'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreSubjectRequest)->attributes();
    }
}
