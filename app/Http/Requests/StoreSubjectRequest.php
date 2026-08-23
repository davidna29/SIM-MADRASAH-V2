<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'unique:subjects,code'],
            'name' => ['required', 'string', 'max:60'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode mata pelajaran',
            'name' => 'nama mata pelajaran',
        ];
    }
}
