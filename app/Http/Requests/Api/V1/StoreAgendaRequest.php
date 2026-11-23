<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:agenda,pengumuman'],
            'tanggal' => ['nullable', 'date'],
            'waktu' => ['nullable', 'date_format:H:i'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'penanggung_jawab' => ['nullable', 'string', 'max:100'],
            'isi' => ['nullable', 'string'],
            'target' => ['required', 'in:publik,internal'],
            'tampil_mulai' => ['required', 'date'],
            'tampil_selesai' => ['nullable', 'date', 'after_or_equal:tampil_mulai'],
            'status' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.prohibited' => 'Status tidak boleh dikirim; agenda API selalu dibuat sebagai arsip.',
        ];
    }
}
