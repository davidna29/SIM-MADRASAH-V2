<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy di level controller (EmployeePolicy)
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');
        $personId = $employeeId?->person_id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'digits:16', 'unique:people,nik,'.$personId],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['required', 'string', 'max:20'],
            'birth_place' => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'nip' => ['nullable', 'string', 'max:20', 'unique:employees,nip,'.$employeeId?->id],
            'employee_status' => ['required', 'in:pns,pppk,honor'],
            'status' => ['required', 'in:aktif,cuti,nonaktif'],
            'position_id' => ['required', 'exists:positions,id'],
            'organizational_unit_id' => ['required', 'exists:organizational_units,id'],
            'tmt' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreEmployeeRequest)->attributes();
    }
}
