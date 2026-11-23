<?php

namespace App\Http\Requests;

use App\Models\AcademicCalendarEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicCalendarEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(AcademicCalendarEvent::CATEGORIES))],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_libur' => ['nullable', 'boolean'],
            'is_efektif' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'judul kegiatan',
            'category' => 'kategori',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'is_libur' => 'penanda libur',
            'description' => 'keterangan',
        ];
    }
}
