<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy di level controller (EmployeePolicy)
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'digits:16', 'unique:people,nik'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['required', 'string', 'max:20'],
            'birth_place' => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'nip' => ['nullable', 'string', 'max:20', 'unique:employees,nip'],
            'employee_status' => ['required', 'in:pns,pppk,honor'],
            'status' => ['required', 'in:aktif,cuti,nonaktif'],
            'position_id' => ['required', 'exists:positions,id'],
            'organizational_unit_id' => ['required', 'exists:organizational_units,id'],
            'tmt' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'nik' => 'NIK',
            'gender' => 'jenis kelamin',
            'religion' => 'agama',
            'nip' => 'NIP',
            'employee_status' => 'status pegawai',
            'position_id' => 'jabatan',
            'organizational_unit_id' => 'unit kerja',
            'tmt' => 'TMT',
        ];
    }
}
