<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeachingJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'journal_date' => ['required', 'date', 'before_or_equal:today'],
            'period_no' => ['nullable', 'integer', 'min:1', 'max:20'],
            'materi' => ['required', 'string', 'max:1000'],
            'tujuan' => ['nullable', 'string', 'max:1000'],
            'metode' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'tindak_lanjut' => ['nullable', 'string', 'max:2000'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,ppt,pptx,xls,xlsx', 'max:5120'],
            'status' => ['required', 'in:draft,terisi'],
        ];
    }

    public function attributes(): array
    {
        return [
            'journal_date' => 'tanggal',
            'period_no' => 'jam pelajaran ke',
            'materi' => 'materi pembelajaran',
            'tujuan' => 'tujuan pembelajaran',
            'metode' => 'metode',
            'catatan' => 'catatan kegiatan',
            'tindak_lanjut' => 'tindak lanjut',
            'lampiran' => 'lampiran',
            'status' => 'status jurnal',
        ];
    }
}
