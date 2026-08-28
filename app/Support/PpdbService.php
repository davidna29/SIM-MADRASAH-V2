<?php

namespace App\Support;

use App\Models\Guardian;
use App\Models\Person;
use App\Models\PpdbRegistration;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PpdbService
{
    /**
     * Accept a PPDB registration — salin SELURUH data pendaftaran PERSIS ke master
     * (people/students/guardians + pivot relation). Setelah accept, edit di PPDB
     * dikunci; semua perubahan berikutnya dilakukan di Master Data Siswa.
     *
     * NIS & kelas TIDAK ditentukan di PPDB: Student dibuat tanpa NIS/enrollment.
     * Operator melengkapi lewat modul Data Siswa.
     */
    public static function accept(PpdbRegistration $registration): Student
    {
        // Guard NIK duplikat
        $existingPerson = Person::where('nik', $registration->nik)->first();
        if ($existingPerson) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah terdaftar sebagai "'.$existingPerson->name.'". Periksa kemungkinan data ganda.',
            ]);
        }

        $otherAccepted = PpdbRegistration::where('nik', $registration->nik)
            ->where('id', '!=', $registration->id)
            ->where('status', 'accepted')
            ->first();
        if ($otherAccepted) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah diterima pada pendaftaran "'.$otherAccepted->name.'" ('.$otherAccepted->registration_no.').',
            ]);
        }

        return DB::transaction(function () use ($registration) {
            $map = static::mapping($registration);

            $person = Person::create($map['person']);
            $student = Student::create(['person_id' => $person->id, 'nis' => null, 'name' => $map['student']['name'], 'gender' => $map['student']['gender']] + $map['student']);

            static::attachGuardians($student, $registration);

            $registration->update([
                'status' => 'accepted',
                'student_id' => $student->id,
                'nis_nism' => null,
                'nis_last6' => null,
            ]);

            activity('ppdb')
                ->performedOn($registration)
                ->event('accepted')
                ->withProperties(['student_id' => $student->id])
                ->log('PPDB diterima: '.$registration->name.' (data disalin ke Master Data Siswa; NIS & kelas diisi di Data Siswa)');

            return $student;
        });
    }

    /**
     * Sinkron idempotent dari registrasi accepted ke master (untuk backfill data lama).
     * Tidak membuat Person/Student baru — hanya memperbarui + menyusun guardian.
     */
    public static function syncFromRegistration(PpdbRegistration $registration, Student $student): void
    {
        DB::transaction(function () use ($registration, $student) {
            $map = static::mapping($registration);

            if ($student->person) {
                $student->person->update($map['person']);
            }

            $student->update($map['student']);

            static::attachGuardians($student, $registration);
        });
    }

    /** Pemetaan field PPDB -> master (person & student). IMM: PERNAH -> true. */
    protected static function mapping(PpdbRegistration $r): array
    {
        return [
            'person' => [
                'nik' => $r->nik,
                'name' => strtoupper($r->name),
                'gender' => $r->gender,
                'religion' => $r->religion,
                'birth_place' => $r->birth_place,
                'birth_date' => $r->birth_date,
                'phone' => $r->student_phone,
                'email' => $r->student_email,
                'address' => $r->address,
                'province' => $r->province,
                'city' => $r->city,
                'district' => $r->district,
                'village' => $r->village,
                'rt' => $r->rt,
                'rw' => $r->rw,
                'postal_code' => $r->postal_code,
                'home_phone' => $r->home_phone,
            ],
            'student' => [
                'name' => strtoupper($r->name),
                'gender' => $r->gender,
                'nisn' => $r->nisn,
                'previous_school' => $r->previous_school,
                'origin_school' => $r->origin_school,
                'origin_nsm' => $r->origin_nsm,
                'origin_npsn' => $r->origin_npsn,
                'origin_address' => $r->origin_address,
                'entry_date' => $r->entry_date,
                'hobby' => $r->hobby,
                'ambition' => $r->ambition,
                'child_order' => $r->child_order,
                'sibling_count' => $r->sibling_count,
                'ever_tk' => $r->ever_tk,
                'ever_paud' => $r->ever_paud,
                'residence_type' => $r->residence_type,
                'distance' => $r->distance,
                'transport' => $r->transport,
                'commute_time' => $r->commute_time,
                'kk_number' => $r->kk_number,
                'kk_head_name' => $r->kk_head_name,
                'social_kks' => $r->social_kks,
                'social_pkh' => $r->social_pkh,
                'social_kip' => $r->social_kip,
                'parent_ownership' => $r->parent_ownership,
                'parent_address' => $r->parent_address,
                'parent_province' => $r->parent_province,
                'parent_city' => $r->parent_city,
                'parent_district' => $r->parent_district,
                'parent_village' => $r->parent_village,
                'parent_rt' => $r->parent_rt,
                'parent_rw' => $r->parent_rw,
                'parent_postal_code' => $r->parent_postal_code,
                'imm_hepb' => $r->imm_hepb === 'PERNAH',
                'imm_polio' => $r->imm_polio === 'PERNAH',
                'imm_bcg' => $r->imm_bcg === 'PERNAH',
                'imm_campak' => $r->imm_campak === 'PERNAH',
                'imm_dpt' => $r->imm_dpt === 'PERNAH',
                'imm_covid' => $r->imm_covid === 'PERNAH',
                'dis_deaf' => (bool) $r->dis_deaf,
                'dis_blind' => (bool) $r->dis_blind,
                'dis_disabled' => (bool) $r->dis_disabled,
                'dis_intellectual' => (bool) $r->dis_intellectual,
                'dis_behavioral' => (bool) $r->dis_behavioral,
                'dis_slow_learner' => (bool) $r->dis_slow_learner,
                'dis_communication' => (bool) $r->dis_communication,
                'dis_gifted' => (bool) $r->dis_gifted,
                'documents' => array_filter([
                    'kk' => $r->scanned_kk,
                    'kk_wali' => $r->scanned_kk_wali,
                    'akta' => $r->scanned_akta,
                    'ijazah' => $r->scanned_ijazah,
                    'photo' => $r->scanned_photo,
                ]),
            ],
        ];
    }

    /** Buat/perbarui 3 guardian (ayah/ibu/wali) + pivot relation; dedupe by NIK. */
    protected static function attachGuardians(Student $student, PpdbRegistration $r): void
    {
        $set = [
            ['relation' => 'ayah', 'data' => [
                'name' => $r->father_name,
                'nik' => $r->father_nik,
                'status' => $r->father_status,
                'birth_place' => $r->father_birth_place,
                'birth_date' => $r->father_birth_date,
                'education' => $r->father_education,
                'job' => $r->father_job,
                'income' => $r->father_income,
                'phone' => $r->father_phone,
            ]],
            ['relation' => 'ibu', 'data' => [
                'name' => $r->mother_name,
                'nik' => $r->mother_nik,
                'status' => $r->mother_status,
                'birth_place' => $r->mother_birth_place,
                'birth_date' => $r->mother_birth_date,
                'education' => $r->mother_education,
                'job' => $r->mother_job,
                'income' => $r->mother_income,
                'phone' => $r->mother_phone,
            ]],
            ['relation' => 'wali', 'data' => [
                'name' => $r->guardian_name,
                'nik' => $r->guardian_nik,
                'status' => null,
                'birth_place' => $r->guardian_birth_place,
                'birth_date' => $r->guardian_birth_date,
                'education' => $r->guardian_education,
                'job' => $r->guardian_job,
                'income' => $r->guardian_income,
                'phone' => $r->guardian_phone,
            ]],
        ];

        foreach ($set as $item) {
            $data = $item['data'];
            if (empty($data['name'])) {
                continue;
            }

            $guardian = ! empty($data['nik'])
                ? Guardian::where('nik', $data['nik'])->first()
                : null;

            $guardian ??= Guardian::create(['user_id' => null] + $data);

            if (! $student->guardians()->whereKey($guardian->id)->exists()) {
                $student->guardians()->attach($guardian->id, ['relation' => $item['relation']]);
            }
        }
    }

    /**
     * Export-friendly column mapping for EMIS (tidak berubah dari sebelumnya).
     */
    public static function exportMapping(): array
    {
        return [
            'registration_no' => 'No. Pendaftaran',
            'name' => 'Nama Siswa',
            'nik' => 'NIK',
            'nisn' => 'NISN',
            'gender' => 'Jenis Kelamin',
            'religion' => 'Agama',
            'birth_place' => 'Tempat Lahir',
            'birth_date' => 'Tanggal Lahir',
            'previous_school' => 'Asal Sekolah',
            'hobby' => 'Hobi',
            'ambition' => 'Cita-cita',
            'child_order' => 'Anak Ke',
            'sibling_count' => 'Jumlah Saudara',
            'ever_tk' => 'Pernah TK',
            'ever_paud' => 'Pernah PAUD',
            'entry_date' => 'Tanggal Masuk',
            'scanned_kk' => 'Link KK',
            'scanned_kk_wali' => 'Link KK Wali',
            'scanned_akta' => 'Link Akta',
            'scanned_ijazah' => 'Link Ijazah',
            'scanned_photo' => 'Link Foto',
            'imm_hepb' => 'Imunisasi Hepatitis B',
            'imm_polio' => 'Imunisasi Polio',
            'imm_bcg' => 'Imunisasi BCG',
            'imm_campak' => 'Imunisasi Campak',
            'imm_dpt' => 'Imunisasi DPT-HB-HiB',
            'imm_covid' => 'Vaksin COVID',
            'dis_deaf' => 'Tuna Rungu',
            'dis_blind' => 'Tuna Netra',
            'dis_disabled' => 'Tuna Daksa',
            'dis_intellectual' => 'Tuna Grahita',
            'dis_behavioral' => 'Tuna Laras',
            'dis_slow_learner' => 'Lamban Belajar',
            'dis_communication' => 'Gangguan Komunikasi',
            'dis_gifted' => 'Bakat Luar Biasa',
            'residence_type' => 'Jenis Tempat Tinggal',
            'address' => 'Alamat Siswa',
            'province' => 'Provinsi',
            'city' => 'Kota/Kabupaten',
            'district' => 'Kecamatan',
            'village' => 'Kelurahan',
            'rt' => 'RT',
            'rw' => 'RW',
            'postal_code' => 'Kode Pos',
            'distance' => 'Jarak ke Madrasah',
            'transport' => 'Transportasi',
            'commute_time' => 'Waktu Tempuh',
            'home_phone' => 'Telepon Rumah',
            'student_phone' => 'Telepon Siswa',
            'student_email' => 'Email Siswa',
            'kk_number' => 'No. KK',
            'kk_head_name' => 'Nama Kepala Keluarga',
            'father_name' => 'Nama Ayah',
            'father_status' => 'Status Ayah',
            'father_nik' => 'NIK Ayah',
            'father_birth_place' => 'Tempat Lahir Ayah',
            'father_birth_date' => 'Tgl Lahir Ayah',
            'father_education' => 'Pendidikan Ayah',
            'father_job' => 'Pekerjaan Ayah',
            'father_income' => 'Penghasilan Ayah',
            'father_phone' => 'HP Ayah',
            'mother_name' => 'Nama Ibu',
            'mother_status' => 'Status Ibu',
            'mother_nik' => 'NIK Ibu',
            'mother_birth_place' => 'Tempat Lahir Ibu',
            'mother_birth_date' => 'Tgl Lahir Ibu',
            'mother_education' => 'Pendidikan Ibu',
            'mother_job' => 'Pekerjaan Ibu',
            'mother_income' => 'Penghasilan Ibu',
            'mother_phone' => 'HP Ibu',
            'guardian_name' => 'Nama Wali',
            'guardian_nik' => 'NIK Wali',
            'guardian_birth_place' => 'Tempat Lahir Wali',
            'guardian_birth_date' => 'Tgl Lahir Wali',
            'guardian_education' => 'Pendidikan Wali',
            'guardian_job' => 'Pekerjaan Wali',
            'guardian_income' => 'Penghasilan Wali',
            'guardian_phone' => 'HP Wali',
            'social_kks' => 'No. KKS',
            'social_pkh' => 'No. PKH',
            'social_kip' => 'No. KIP',
            'parent_ownership' => 'Status Rumah',
            'parent_address' => 'Alamat Orang Tua',
            'parent_province' => 'Provinsi OT',
            'parent_city' => 'Kota OT',
            'parent_district' => 'Kecamatan OT',
            'parent_village' => 'Kelurahan OT',
            'parent_rt' => 'RT OT',
            'parent_rw' => 'RW OT',
            'parent_postal_code' => 'Kode Pos OT',
            'origin_school' => 'Sekolah Asal',
            'origin_nsm' => 'NSM Sekolah Asal',
            'origin_npsn' => 'NPSN Sekolah Asal',
            'origin_address' => 'Alamat Sekolah Asal',
            'kelas' => 'Kelas',
            'rombel' => 'Rombel',
            'nis_nism' => 'NIS/NISM',
            'nis_last6' => 'NIS 6 Digit',
            'status' => 'Status Pendaftaran',
        ];
    }
}
