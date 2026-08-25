<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCounselingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_enrollment_id' => ['required', 'exists:student_enrollments,id'],
            'session_date' => ['required', 'date', 'before_or_equal:today'],
            'counseling_type' => ['required', 'string', 'in:individual,kelompok,krisis'],
            'topic' => ['required', 'string', 'max:255'],
            'problem_description' => ['nullable', 'string', 'max:5000'],
            'assessment_result' => ['nullable', 'string', 'max:5000'],
            'action_taken' => ['nullable', 'string', 'max:5000'],
            'follow_up_plan' => ['nullable', 'string', 'max:5000'],
            'confidentiality_level' => ['required', 'string', 'in:guru_bk_only,plus_kepala,plus_wali_kelas'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_enrollment_id' => 'siswa',
            'session_date' => 'tanggal sesi',
            'counseling_type' => 'jenis konseling',
            'topic' => 'topik',
            'problem_description' => 'permasalahan',
            'assessment_result' => 'hasil asesmen',
            'action_taken' => 'tindakan',
            'follow_up_plan' => 'rencana tindak lanjut',
            'confidentiality_level' => 'tingkat kerahasiaan',
            'attachment' => 'lampiran',
        ];
    }
}
