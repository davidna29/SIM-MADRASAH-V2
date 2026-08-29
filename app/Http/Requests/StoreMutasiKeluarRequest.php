<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\StudentMutation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMutasiKeluarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'tanggal_mutasi' => ['required', 'date'],
            'sekolah_tujuan' => ['required', 'string', 'max:100'],
            'tujuan_nsm' => ['nullable', 'string', 'max:12'],
            'tujuan_npsn' => ['nullable', 'string', 'max:8'],
            'alasan_pindah' => ['required', Rule::in(['pindah_ortu', 'pindah_alamat', 'keluarga', 'lainnya'])],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'no_surat' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Pada update, siswa sudah berstatus 'keluar'; cek aktif/duplikat hanya saat create.
                if ($this->route('mutation')) {
                    return;
                }

                $yearId = AcademicYear::active()?->id;

                // Pastikan siswa ter-enroll aktif pada tahun berjalan.
                $enrollment = StudentEnrollment::query()
                    ->where('student_id', $this->input('student_id'))
                    ->where('academic_year_id', $yearId)
                    ->where('status', 'aktif')
                    ->exists();

                if (! $enrollment) {
                    $validator->errors()->add('student_id', 'Siswa tidak memiliki penempatan aktif pada tahun ajaran berjalan.');
                }

                // Anti-duplikat: siswa sudah punya mutasi keluar pada tahun ajaran yang sama.
                $exists = StudentMutation::query()
                    ->where('student_id', $this->input('student_id'))
                    ->where('academic_year_id', $yearId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('student_id', 'Siswa ini sudah tercatat mutasi keluar pada tahun ajaran yang sama.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id' => 'siswa',
            'tanggal_mutasi' => 'tanggal mutasi',
            'sekolah_tujuan' => 'sekolah tujuan',
            'tujuan_nsm' => 'NSM tujuan',
            'tujuan_npsn' => 'NPSN tujuan',
            'alasan_pindah' => 'alasan pindah',
            'keterangan' => 'keterangan',
            'no_surat' => 'nomor surat',
        ];
    }
}
