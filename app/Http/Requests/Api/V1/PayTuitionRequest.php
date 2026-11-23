<?php

namespace App\Http\Requests\Api\V1;

use App\Models\TuitionPayment;
use Illuminate\Foundation\Http\FormRequest;

class PayTuitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', TuitionPayment::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'student_enrollment_id' => ['required', 'integer', 'exists:student_enrollments,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tanggal_bayar' => ['nullable', 'date', 'before_or_equal:today'],
            'metode' => ['nullable', 'in:tunai,transfer'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'external_reference' => ['required', 'string', 'min:4', 'max:100'],
            'nominal' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'nominal.prohibited' => 'Nominal tidak boleh dikirim; nominal dihitung server-side.',
        ];
    }
}
