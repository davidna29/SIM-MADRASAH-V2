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

            // Asal sekolah
            'origin_school' => 'required|string|max:100',
            'origin_nsm' => 'nullable|string|digits:12',
            'origin_npsn' => 'nullable|string|digits:8',
            'origin_address' => 'nullable|string|max:255',
            'kelas_asal' => 'required|string|max:20',

            // Tujuan mutasi
            'kelas_tujuan' => 'required|string|max:20',
            'alasan_pindah' => 'required|string|max:1000',
            'tanggal_mutasi' => 'nullable|date',

            // Alamat
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:60',
            'city' => 'required|string|max:60',
            'district' => 'required|string|max:60',
            'village' => 'nullable|string|max:60',
            'rt' => 'nullable|digits_between:1,3',
            'rw' => 'nullable|digits_between:1,3',
            'postal_code' => 'nullable|string|digits:5',
            'student_phone' => 'required|string|max:20',
            'student_email' => 'nullable|email|max:100',

            // Orang tua / wali (ringkas)
            'father_name' => 'required|string|max:100',
            'father_nik' => 'nullable|string|digits:16',
            'father_job' => 'nullable|string|max:30',
            'father_phone' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:100',
            'mother_nik' => 'nullable|string|digits:16',
            'mother_job' => 'nullable|string|max:30',
            'mother_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_nik' => 'nullable|string|digits:16',
            'guardian_phone' => 'nullable|string|max:20',

            // Dokumen — Surat Rekomendasi Madrasah WAJIB
            'scanned_rekomendasi' => 'required|url|max:500',
            'scanned_rapor' => 'nullable|url|max:500',
            'scanned_kk' => 'nullable|url|max:500',
            'scanned_akta' => 'nullable|url|max:500',
            'scanned_photo' => 'nullable|url|max:500',
        ];
    }
}
