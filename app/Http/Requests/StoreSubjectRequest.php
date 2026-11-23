<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'category' => ['nullable', Rule::in(['keagamaan', 'umum', 'mulok'])],
            'jp' => ['nullable', 'array'],
            'jp.*' => ['nullable', 'integer', 'min:0', 'max:12'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode mata pelajaran',
            'name' => 'nama mata pelajaran',
            'category' => 'kelompok mata pelajaran',
            'jp' => 'alokasi JP',
        ];
    }
}
