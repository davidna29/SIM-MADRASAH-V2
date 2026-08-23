<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_assignment_id' => ['required', 'exists:teacher_assignments,id'],
            'day' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:40'],
        ];
    }

    public function attributes(): array
    {
        return [
            'teacher_assignment_id' => 'penugasan (guru-mapel-kelas)',
            'day' => 'hari',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
            'room' => 'ruang',
        ];
    }
}
