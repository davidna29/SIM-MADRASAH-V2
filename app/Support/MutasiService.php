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
     * Accept pendaftar pindah — salin PERSIS ke master (people/students/guardians).
     * Setelah accept, edit di modul mutasi dikunci; data dikelola di Data Siswa.
     *
     * NIS & kelas dilengkapi belakangan via Data Siswa (kelas tujuan tercatat di
     * registrasi sebagai acuan operator).
     */
    public static function accept(MutasiRegistration $registration): Student
    {
        $existingPerson = Person::where('nik', $registration->nik)->first();
        if ($existingPerson) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah terdaftar sebagai "'.$existingPerson->name.'". Periksa kemungkinan data ganda.',
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
            ]);

            $student = Student::create([
                'person_id' => $person->id,
                'nis' => null,
                'name' => strtoupper($registration->name),
                'gender' => $registration->gender,
                'nisn' => $registration->nisn,
                'origin_school' => $registration->origin_school,
                'origin_nsm' => $registration->origin_nsm,
                'origin_npsn' => $registration->origin_npsn,
                'origin_address' => $registration->origin_address,
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
            ['relation' => 'ayah', 'name' => $r->father_name, 'nik' => $r->father_nik, 'job' => $r->father_job, 'phone' => $r->father_phone],
            ['relation' => 'ibu', 'name' => $r->mother_name, 'nik' => $r->mother_nik, 'job' => $r->mother_job, 'phone' => $r->mother_phone],
            ['relation' => 'wali', 'name' => $r->guardian_name, 'nik' => $r->guardian_nik, 'job' => null, 'phone' => $r->guardian_phone],
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
                'job' => $item['job'],
                'phone' => $item['phone'],
            ]);

            if (! $student->guardians()->whereKey($guardian->id)->exists()) {
                $student->guardians()->attach($guardian->id, ['relation' => $item['relation']]);
            }
        }
    }
}
