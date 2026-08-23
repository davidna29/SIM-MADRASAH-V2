<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis'],
            'nik' => ['required', 'digits:16', 'unique:people,nik'],
            'name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['nullable', 'string', 'max:20'],
            'birth_place' => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis' => 'NIS',
            'nik' => 'NIK',
            'name' => 'nama lengkap',
            'gender' => 'jenis kelamin',
            'class_group_id' => 'kelas',
        ];
    }
}
