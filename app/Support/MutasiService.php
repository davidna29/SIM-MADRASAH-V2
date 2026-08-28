<?php

namespace App\Support;

use App\Models\Guardian;
use App\Models\MutasiRegistration;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MutasiService
{
    /**
     * Accept pendaftar pindah — salin PERSIS ke master.
     * Edit di modul mutasi dikunci setelah accept.
     */
    public static function accept(MutasiRegistration $registration): Student
    {
        $existingPerson = Person::where('nik', $registration->nik)->first();
        if ($existingPerson) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah terdaftar sebagai "'.$existingPerson->name.'".',
            ]);
        }

        $otherAccepted = MutasiRegistration::where('nik', $registration->nik)
            ->where('id', '!=', $registration->id)
            ->where('status', 'accepted')
            ->first();
        if ($otherAccepted) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah diterima pada pendaftaran pindah "'.$otherAccepted->name.'" ('.$otherAccepted->registration_no.').',
            ]);
        }

        return DB::transaction(function () use ($registration) {
            $person = Person::create([
                'nik' => $registration->nik,
                'name' => strtoupper($registration->name),
                'gender' => $registration->gender,
                'religion' => $registration->religion,
                'birth_place' => $registration->birth_place,
                'birth_date' => $registration->birth_date,
                'phone' => $registration->student_phone,
                'email' => $registration->student_email,
                'address' => $registration->address,
                'province' => $registration->province,
                'city' => $registration->city,
                'district' => $registration->district,
                'village' => $registration->village,
                'rt' => $registration->rt,
                'rw' => $registration->rw,
                'postal_code' => $registration->postal_code,
                'home_phone' => $registration->home_phone,
            ]);

            $student = Student::create([
                'person_id' => $person->id,
                'nis' => null,
                'name' => strtoupper($registration->name),
                'gender' => $registration->gender,
                'nisn' => $registration->nisn,
                'previous_school' => $registration->previous_school,
                'hobby' => $registration->hobby,
                'ambition' => $registration->ambition,
                'child_order' => $registration->child_order,
                'sibling_count' => $registration->sibling_count,
                'ever_tk' => $registration->ever_tk,
                'ever_paud' => $registration->ever_paud,
                'entry_date' => $registration->entry_date,
                'residence_type' => $registration->residence_type,
                'distance' => $registration->distance,
                'transport' => $registration->transport,
                'commute_time' => $registration->commute_time,
                'kk_number' => $registration->kk_number,
                'kk_head_name' => $registration->kk_head_name,
                'origin_school' => $registration->origin_school,
                'origin_nsm' => $registration->origin_nsm,
                'origin_npsn' => $registration->origin_npsn,
                'origin_address' => $registration->origin_address,
                'social_kks' => $registration->social_kks,
                'social_pkh' => $registration->social_pkh,
                'social_kip' => $registration->social_kip,
                'parent_ownership' => $registration->parent_ownership,
                'parent_address' => $registration->parent_address,
                'parent_province' => $registration->parent_province,
                'parent_city' => $registration->parent_city,
                'parent_district' => $registration->parent_district,
                'parent_village' => $registration->parent_village,
                'parent_rt' => $registration->parent_rt,
                'parent_rw' => $registration->parent_rw,
                'parent_postal_code' => $registration->parent_postal_code,
                'imm_hepb' => (bool) $registration->imm_hepb,
                'imm_polio' => (bool) $registration->imm_polio,
                'imm_bcg' => (bool) $registration->imm_bcg,
                'imm_campak' => (bool) $registration->imm_campak,
                'imm_dpt' => (bool) $registration->imm_dpt,
                'imm_covid' => (bool) $registration->imm_covid,
                'dis_deaf' => (bool) $registration->dis_deaf,
                'dis_blind' => (bool) $registration->dis_blind,
                'dis_disabled' => (bool) $registration->dis_disabled,
                'dis_intellectual' => (bool) $registration->dis_intellectual,
                'dis_behavioral' => (bool) $registration->dis_behavioral,
                'dis_slow_learner' => (bool) $registration->dis_slow_learner,
                'dis_communication' => (bool) $registration->dis_communication,
                'dis_gifted' => (bool) $registration->dis_gifted,
                'documents' => array_filter([
                    'rekomendasi' => $registration->scanned_rekomendasi,
                    'rapor' => $registration->scanned_rapor,
                    'kk' => $registration->scanned_kk,
                    'kk_wali' => $registration->scanned_kk_wali,
                    'akta' => $registration->scanned_akta,
                    'ijazah' => $registration->scanned_ijazah,
                    'photo' => $registration->scanned_photo,
                ]),
            ]);

            static::attachGuardians($student, $registration);

            $registration->update([
                'status' => 'accepted',
                'student_id' => $student->id,
            ]);

            activity('mutasi')
                ->performedOn($registration)
                ->event('accepted')
                ->withProperties(['student_id' => $student->id])
                ->log('Mutasi diterima: '.$registration->name.' (data disalin ke Master Data Siswa)');

            return $student;
        });
    }

    /** Buat ayah/ibu/wali + pivot relation; dedupe by NIK. */
    protected static function attachGuardians(Student $student, MutasiRegistration $r): void
    {
        $set = [
            ['relation' => 'ayah', 'name' => $r->father_name, 'nik' => $r->father_nik,
                'status' => $r->father_status, 'birth_place' => $r->father_birth_place, 'birth_date' => $r->father_birth_date,
                'education' => $r->father_education, 'job' => $r->father_job, 'income' => $r->father_income, 'phone' => $r->father_phone],
            ['relation' => 'ibu', 'name' => $r->mother_name, 'nik' => $r->mother_nik,
                'status' => $r->mother_status, 'birth_place' => $r->mother_birth_place, 'birth_date' => $r->mother_birth_date,
                'education' => $r->mother_education, 'job' => $r->mother_job, 'income' => $r->mother_income, 'phone' => $r->mother_phone],
            ['relation' => 'wali', 'name' => $r->guardian_name, 'nik' => $r->guardian_nik,
                'status' => null, 'birth_place' => null, 'birth_date' => $r->guardian_birth_date,
                'education' => $r->guardian_education, 'job' => $r->guardian_job, 'income' => $r->guardian_income, 'phone' => $r->guardian_phone],
        ];

        foreach ($set as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $guardian = ! empty($item['nik'])
                ? Guardian::where('nik', $item['nik'])->first()
                : null;

            $guardian ??= Guardian::create([
                'user_id' => null,
                'name' => $item['name'],
                'nik' => $item['nik'],
                'status' => $item['status'],
                'birth_place' => $item['birth_place'],
                'birth_date' => $item['birth_date'],
                'education' => $item['education'],
                'job' => $item['job'],
                'income' => $item['income'],
                'phone' => $item['phone'],
            ]);

            if (! $student->guardians()->whereKey($guardian->id)->exists()) {
                $student->guardians()->attach($guardian->id, ['relation' => $item['relation']]);
            }
        }
    }
}
