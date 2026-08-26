<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PpdbRegistrationRequest extends FormRequest
{
    /**
     * Diizinkan untuk publik (submit) dan admin (edit calon siswa).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi mengikuti field formulir PPDB.
     * RT/RW menerima 1-3 digit (digits_between:1,3).
     */
    public function rules(): array
    {
        return [
            // A. Data Siswa
            'name' => 'required|string|max:100',
            'nik' => 'required|string|digits:16',
            'nisn' => 'nullable|string|digits:10',
            'gender' => 'required|in:L,P',
            'religion' => 'required|string|max:20',
            'birth_place' => 'required|string|max:60',
            'birth_date' => 'required|date|before:today',
            'previous_school' => 'nullable|string|max:100',
            'hobby' => 'required|string|max:60',
            'ambition' => 'required|string|max:60',
            'child_order' => 'required|integer|min:1|max:9',
            'sibling_count' => 'required|integer|min:0|max:9',
            'ever_tk' => 'required|in:PERNAH,TIDAK',
            'ever_paud' => 'required|in:PERNAH,TIDAK',
            'entry_date' => 'nullable|date',

            // Dokumen (Google Drive links)
            'scanned_kk' => 'required|url|max:500',
            'scanned_kk_wali' => 'nullable|url|max:500',
            'scanned_akta' => 'required|url|max:500',
            'scanned_ijazah' => 'nullable|url|max:500',
            'scanned_photo' => 'nullable|url|max:500',

            // B. Imunisasi
            'imm_hepb' => 'required|in:PERNAH,TIDAK',
            'imm_polio' => 'required|in:PERNAH,TIDAK',
            'imm_bcg' => 'required|in:PERNAH,TIDAK',
            'imm_campak' => 'required|in:PERNAH,TIDAK',
            'imm_dpt' => 'required|in:PERNAH,TIDAK',
            'imm_covid' => 'required|in:PERNAH,TIDAK',

            // C. Berkebutuhan Khusus
            'dis_deaf' => 'boolean',
            'dis_blind' => 'boolean',
            'dis_disabled' => 'boolean',
            'dis_intellectual' => 'boolean',
            'dis_behavioral' => 'boolean',
            'dis_slow_learner' => 'boolean',
            'dis_communication' => 'boolean',
            'dis_gifted' => 'boolean',

            // D. Alamat Siswa
            'residence_type' => 'required|string|max:60',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:60',
            'city' => 'required|string|max:60',
            'district' => 'required|string|max:60',
            'village' => 'required|string|max:60',
            'rt' => 'required|digits_between:1,3',
            'rw' => 'required|digits_between:1,3',
            'postal_code' => 'required|string|digits:5',
            'distance' => 'required|string|max:20',
            'transport' => 'required|string|max:60',
            'commute_time' => 'required|string|max:30',
            'home_phone' => 'nullable|string|max:20',
            'student_phone' => 'nullable|string|max:20',
            'student_email' => 'nullable|email|max:100',

            // E. Orang Tua / Wali
            'kk_number' => 'required|string|digits:16',
            'kk_head_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'father_status' => 'required|string|max:30',
            'father_nik' => 'nullable|string|digits:16',
            'father_birth_place' => 'nullable|string|max:60',
            'father_birth_date' => 'nullable|date',
            'father_education' => 'nullable|string|max:30',
            'father_job' => 'nullable|string|max:30',
            'father_income' => 'nullable|string|max:30',
            'father_phone' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:100',
            'mother_status' => 'required|string|max:30',
            'mother_nik' => 'required|string|digits:16',
            'mother_birth_place' => 'nullable|string|max:60',
            'mother_birth_date' => 'required|date',
            'mother_education' => 'required|string|max:30',
            'mother_job' => 'required|string|max:30',
            'mother_income' => 'required|string|max:30',
            'mother_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_nik' => 'nullable|string|digits:16',
            'guardian_birth_place' => 'nullable|string|max:60',
            'guardian_birth_date' => 'nullable|date',
            'guardian_education' => 'nullable|string|max:30',
            'guardian_job' => 'nullable|string|max:30',
            'guardian_income' => 'nullable|string|max:30',
            'guardian_phone' => 'nullable|string|max:20',
            'social_kks' => 'nullable|string|max:30',
            'social_pkh' => 'nullable|string|max:30',
            'social_kip' => 'nullable|string|max:30',

            // F. Alamat Orang Tua
            'parent_ownership' => 'required|string|max:40',
            'parent_address' => 'required|string|max:255',
            'parent_province' => 'required|string|max:60',
            'parent_city' => 'required|string|max:60',
            'parent_district' => 'required|string|max:60',
            'parent_village' => 'required|string|max:60',
            'parent_rt' => 'required|digits_between:1,3',
            'parent_rw' => 'required|digits_between:1,3',
            'parent_postal_code' => 'required|string|digits:5',

            // G. Sekolah Asal
            'origin_school' => 'required|string|max:100',
            'origin_nsm' => 'nullable|string|digits:12',
            'origin_npsn' => 'nullable|string|digits:8',
            'origin_address' => 'nullable|string|max:255',
        ];
    }
}
