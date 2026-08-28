<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis,'.$student?->id],
            'nik' => ['required', 'digits:16', 'unique:people,nik,'.$student?->person_id],
            'name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['nullable', 'string', 'max:20'],
            'birth_place' => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:60'],
            'city' => ['nullable', 'string', 'max:60'],
            'district' => ['nullable', 'string', 'max:60'],
            'village' => ['nullable', 'string', 'max:60'],
            'rt' => ['nullable', 'string', 'max:3'],
            'rw' => ['nullable', 'string', 'max:3'],
            'postal_code' => ['nullable', 'string', 'max:5'],
            'home_phone' => ['nullable', 'string', 'max:20'],
            'class_group_id' => ['nullable', 'exists:class_groups,id'],

            // Profil akademik (master)
            'nisn' => ['nullable', 'string', 'max:10'],
            'previous_school' => ['nullable', 'string', 'max:100'],
            'origin_school' => ['nullable', 'string', 'max:100'],
            'origin_nsm' => ['nullable', 'string', 'max:12'],
            'origin_npsn' => ['nullable', 'string', 'max:8'],
            'origin_address' => ['nullable', 'string', 'max:255'],
            'entry_date' => ['nullable', 'date'],
            'hobby' => ['nullable', 'string', 'max:60'],
            'ambition' => ['nullable', 'string', 'max:60'],
            'child_order' => ['nullable', 'integer', 'min:0', 'max:99'],
            'sibling_count' => ['nullable', 'integer', 'min:0', 'max:99'],
            'ever_tk' => ['nullable', 'in:PERNAH,TIDAK'],
            'ever_paud' => ['nullable', 'in:PERNAH,TIDAK'],
            'residence_type' => ['nullable', 'string', 'max:60'],
            'distance' => ['nullable', 'string', 'max:20'],
            'transport' => ['nullable', 'string', 'max:60'],
            'commute_time' => ['nullable', 'string', 'max:30'],
            'kk_number' => ['nullable', 'string', 'max:16'],
            'kk_head_name' => ['nullable', 'string', 'max:100'],
            'social_kks' => ['nullable', 'string', 'max:30'],
            'social_pkh' => ['nullable', 'string', 'max:30'],
            'social_kip' => ['nullable', 'string', 'max:30'],
            'parent_ownership' => ['nullable', 'string', 'max:40'],
            'parent_address' => ['nullable', 'string', 'max:255'],
            'parent_province' => ['nullable', 'string', 'max:60'],
            'parent_city' => ['nullable', 'string', 'max:60'],
            'parent_district' => ['nullable', 'string', 'max:60'],
            'parent_village' => ['nullable', 'string', 'max:60'],
            'parent_rt' => ['nullable', 'string', 'max:3'],
            'parent_rw' => ['nullable', 'string', 'max:3'],
            'parent_postal_code' => ['nullable', 'string', 'max:5'],
            'imm_hepb' => ['nullable', 'boolean'],
            'imm_polio' => ['nullable', 'boolean'],
            'imm_bcg' => ['nullable', 'boolean'],
            'imm_campak' => ['nullable', 'boolean'],
            'imm_dpt' => ['nullable', 'boolean'],
            'imm_covid' => ['nullable', 'boolean'],
            'dis_deaf' => ['nullable', 'boolean'],
            'dis_blind' => ['nullable', 'boolean'],
            'dis_disabled' => ['nullable', 'boolean'],
            'dis_intellectual' => ['nullable', 'boolean'],
            'dis_behavioral' => ['nullable', 'boolean'],
            'dis_slow_learner' => ['nullable', 'boolean'],
            'dis_communication' => ['nullable', 'boolean'],
            'dis_gifted' => ['nullable', 'boolean'],
            'scanned_kk' => ['nullable', 'string', 'max:500'],
            'scanned_kk_wali' => ['nullable', 'string', 'max:500'],
            'scanned_akta' => ['nullable', 'string', 'max:500'],
            'scanned_ijazah' => ['nullable', 'string', 'max:500'],
            'scanned_photo' => ['nullable', 'string', 'max:500'],

            // Guardian
            'father_id' => ['nullable', 'exists:guardians,id'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'father_status' => ['nullable', 'string', 'max:30'],
            'father_nik' => ['nullable', 'string', 'max:16'],
            'father_birth_place' => ['nullable', 'string', 'max:60'],
            'father_birth_date' => ['nullable', 'date'],
            'father_education' => ['nullable', 'string', 'max:30'],
            'father_job' => ['nullable', 'string', 'max:30'],
            'father_income' => ['nullable', 'string', 'max:30'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'mother_id' => ['nullable', 'exists:guardians,id'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'mother_status' => ['nullable', 'string', 'max:30'],
            'mother_nik' => ['nullable', 'string', 'max:16'],
            'mother_birth_place' => ['nullable', 'string', 'max:60'],
            'mother_birth_date' => ['nullable', 'date'],
            'mother_education' => ['nullable', 'string', 'max:30'],
            'mother_job' => ['nullable', 'string', 'max:30'],
            'mother_income' => ['nullable', 'string', 'max:30'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'guardian_id' => ['nullable', 'exists:guardians,id'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'guardian_nik' => ['nullable', 'string', 'max:16'],
            'guardian_birth_place' => ['nullable', 'string', 'max:60'],
            'guardian_birth_date' => ['nullable', 'date'],
            'guardian_education' => ['nullable', 'string', 'max:30'],
            'guardian_job' => ['nullable', 'string', 'max:30'],
            'guardian_income' => ['nullable', 'string', 'max:30'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreStudentRequest)->attributes();
    }
}
