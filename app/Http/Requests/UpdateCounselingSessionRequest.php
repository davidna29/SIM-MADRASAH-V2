<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCounselingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $session = $this->route('session');

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
            'status' => ['required', 'string', 'in:aktif,ditutup'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreCounselingSessionRequest)->attributes();
    }
}
