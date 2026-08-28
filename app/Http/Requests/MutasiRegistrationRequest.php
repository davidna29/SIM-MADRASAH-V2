<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MutasiRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // A. Identitas Siswa
            'name' => 'required|string|max:100',
            'nik' => 'required|string|digits:16',
            'nisn' => 'nullable|string|digits:10',
            'nis_asal' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'religion' => 'required|string|max:20',
            'birth_place' => 'nullable|string|max:60',
            'birth_date' => 'nullable|date|before:today',
            'previous_school' => 'nullable|string|max:100',
            'hobby' => 'nullable|string|max:60',
            'ambition' => 'nullable|string|max:60',
            'child_order' => 'nullable|integer|min:0|max:99',
            'sibling_count' => 'nullable|integer|min:0|max:99',
            'ever_tk' => 'nullable|in:PERNAH,TIDAK',
            'ever_paud' => 'nullable|in:PERNAH,TIDAK',
            'entry_date' => 'nullable|date',

            // B. Kesehatan
            'imm_hepb' => 'nullable|boolean',
            'imm_polio' => 'nullable|boolean',
            'imm_bcg' => 'nullable|boolean',
            'imm_campak' => 'nullable|boolean',
            'imm_dpt' => 'nullable|boolean',
            'imm_covid' => 'nullable|boolean',
            'dis_deaf' => 'nullable|boolean',
            'dis_blind' => 'nullable|boolean',
            'dis_disabled' => 'nullable|boolean',
            'dis_intellectual' => 'nullable|boolean',
            'dis_behavioral' => 'nullable|boolean',
            'dis_slow_learner' => 'nullable|boolean',
            'dis_communication' => 'nullable|boolean',
            'dis_gifted' => 'nullable|boolean',

            // C. Asal sekolah
            'origin_school' => 'required|string|max:100',
            'origin_nsm' => 'nullable|string|digits:12',
            'origin_npsn' => 'nullable|string|digits:8',
            'origin_address' => 'nullable|string|max:255',
            'kelas_asal' => 'required|string|max:20',

            // D. Tujuan mutasi
            'kelas_tujuan' => 'required|string|max:20',
            'alasan_pindah' => 'required|string|max:1000',
            'tanggal_mutasi' => 'nullable|date',

            // E. Alamat siswa
            'residence_type' => 'nullable|string|max:60',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:60',
            'city' => 'required|string|max:60',
            'district' => 'required|string|max:60',
            'village' => 'nullable|string|max:60',
            'rt' => 'nullable|digits_between:1,3',
            'rw' => 'nullable|digits_between:1,3',
            'postal_code' => 'nullable|string|digits:5',
            'distance' => 'nullable|string|max:20',
            'transport' => 'nullable|string|max:60',
            'commute_time' => 'nullable|string|max:30',
            'home_phone' => 'nullable|string|max:20',
            'student_phone' => 'required|string|max:20',
            'student_email' => 'nullable|email|max:100',

            // F. Keluarga / KK
            'kk_number' => 'nullable|string|max:16',
            'kk_head_name' => 'nullable|string|max:100',
            'father_name' => 'required|string|max:100',
            'father_status' => 'nullable|string|max:30',
            'father_nik' => 'nullable|string|digits:16',
            'father_birth_place' => 'nullable|string|max:60',
            'father_birth_date' => 'nullable|date',
            'father_education' => 'nullable|string|max:30',
            'father_job' => 'nullable|string|max:30',
            'father_income' => 'nullable|string|max:30',
            'father_phone' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:100',
            'mother_status' => 'nullable|string|max:30',
            'mother_nik' => 'nullable|string|digits:16',
            'mother_birth_place' => 'nullable|string|max:60',
            'mother_birth_date' => 'nullable|date',
            'mother_education' => 'nullable|string|max:30',
            'mother_job' => 'nullable|string|max:30',
            'mother_income' => 'nullable|string|max:30',
            'mother_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_nik' => 'nullable|string|digits:16',
            'guardian_birth_date' => 'nullable|date',
            'guardian_education' => 'nullable|string|max:30',
            'guardian_job' => 'nullable|string|max:30',
            'guardian_income' => 'nullable|string|max:30',
            'guardian_phone' => 'nullable|string|max:20',

            // G. Bantuan sosial
            'social_kks' => 'nullable|string|max:30',
            'social_pkh' => 'nullable|string|max:30',
            'social_kip' => 'nullable|string|max:30',

            // H. Alamat orang tua
            'parent_ownership' => 'nullable|string|max:40',
            'parent_address' => 'nullable|string|max:255',
            'parent_province' => 'nullable|string|max:60',
            'parent_city' => 'nullable|string|max:60',
            'parent_district' => 'nullable|string|max:60',
            'parent_village' => 'nullable|string|max:60',
            'parent_rt' => 'nullable|digits_between:1,3',
            'parent_rw' => 'nullable|digits_between:1,3',
            'parent_postal_code' => 'nullable|string|digits:5',

            // I. Dokumen
            'scanned_rekomendasi' => 'required|url|max:500',
            'scanned_rapor' => 'nullable|url|max:500',
            'scanned_kk' => 'nullable|url|max:500',
            'scanned_kk_wali' => 'nullable|url|max:500',
            'scanned_akta' => 'nullable|url|max:500',
            'scanned_ijazah' => 'nullable|url|max:500',
            'scanned_photo' => 'nullable|url|max:500',
        ];
    }
}
