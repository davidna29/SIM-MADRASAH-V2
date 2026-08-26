<?php

namespace App\Support;

use App\Models\Guardian;
use App\Models\NisCounter;
use App\Models\Person;
use App\Models\PpdbRegistration;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class PpdbService
{
    /**
     * Generate NIS/NISM: NSM (12) + Tahun Masuk (2) + Nomor Urut (4) = 18 digit.
     */
    public static function generateNis(PpdbRegistration $registration): string
    {
        $nsm = Setting::get('madrasah_nsm', '000000000000');
        $nsm = str_pad($nsm, 12, '0', STR_PAD_LEFT);

        $year = $registration->academicYear
            ? substr($registration->academicYear->name, -2)
            : substr((string) now()->year, -2);

        $nextNumber = NisCounter::nextNumber($registration->academic_year_id);
        $nomorUrut = str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

        return $nsm.$year.$nomorUrut;
    }

    /**
     * Preview NIS without incrementing counter.
     */
    public static function previewNis(PpdbRegistration $registration): string
    {
        $nsm = Setting::get('madrasah_nsm', '000000000000');
        $nsm = str_pad($nsm, 12, '0', STR_PAD_LEFT);

        $year = $registration->academicYear
            ? substr($registration->academicYear->name, -2)
            : substr((string) now()->year, -2);

        $nextNumber = NisCounter::peekNext($registration->academic_year_id);
        $nomorUrut = str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

        return $nsm.$year.$nomorUrut;
    }

    /**
     * Accept a PPDB registration — create Student, Person, Guardian, Enrollment.
     */
    public static function accept(PpdbRegistration $registration): Student
    {
        return DB::transaction(function () use ($registration) {
            // 1. Create Person
            $person = Person::create([
                'nik' => $registration->nik,
                'name' => strtoupper($registration->name),
                'gender' => $registration->gender,
                'religion' => $registration->religion,
                'birth_place' => $registration->birth_place,
                'birth_date' => $registration->birth_date,
                'phone' => $registration->student_phone,
                'email' => $registration->student_email,
            ]);

            // 2. Generate NIS
            $nis = self::generateNis($registration);
            $nisLast6 = substr($nis, -6);

            // 3. Create Student
            $student = Student::create([
                'person_id' => $person->id,
                'nis' => $nis,
                'name' => strtoupper($registration->name),
                'gender' => $registration->gender,
            ]);

            // 4. Create Guardian
            $guardian = Guardian::create([
                'user_id' => null,
                'name' => $registration->father_name ?? $registration->mother_name ?? $registration->guardian_name ?? '-',
            ]);

            \DB::table('guardian_student')->insert([
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
            ]);

            // 5. Enrollment created later when class is assigned (class_group_id NOT NULL)

            // 6. Update registration
            $registration->update([
                'status' => 'accepted',
                'student_id' => $student->id,
                'nis_nism' => $nis,
                'nis_last6' => $nisLast6,
            ]);

            activity('ppdb')
                ->performedOn($registration)
                ->event('accepted')
                ->withProperties(['student_id' => $student->id, 'nis' => $nis])
                ->log('PPDB diterima: '.$registration->name.' → NIS '.$nis);

            return $student;
        });
    }

    /**
     * Batch generate NIS for all accepted registrations (sorted by name).
     */
    public static function batchGenerateNis(int $academicYearId): array
    {
        $registrations = PpdbRegistration::where('academic_year_id', $academicYearId)
            ->where('status', 'accepted')
            ->whereNull('nis_nism')
            ->orderByRaw('UPPER(name)')
            ->get();

        $results = [];

        foreach ($registrations as $reg) {
            $nis = self::generateNis($reg);
            $nisLast6 = substr($nis, -6);

            $reg->update([
                'nis_nism' => $nis,
                'nis_last6' => $nisLast6,
            ]);

            $results[] = [
                'registration_no' => $reg->registration_no,
                'name' => $reg->name,
                'nis' => $nis,
                'nis_last6' => $nisLast6,
            ];
        }

        return $results;
    }

    /**
     * Export-friendly column mapping for EMIS.
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
            'previous_school' => 'Sekolah Asal',
            'hobby' => 'Hobi',
            'ambition' => 'Cita-cita',
            'child_order' => 'Anak Ke',
            'sibling_count' => 'Jumlah Saudara',
            'ever_tk' => 'Pernah TK',
            'ever_paud' => 'Pernah PAUD',
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
            'kk_number' => 'No. KK',
            'kk_head_name' => 'Nama Kepala Keluarga',
            'father_name' => 'Nama Ayah',
            'father_status' => 'Status Ayah',
            'father_nik' => 'NIK Ayah',
            'father_education' => 'Pendidikan Ayah',
            'father_job' => 'Pekerjaan Ayah',
            'father_income' => 'Penghasilan Ayah',
            'father_phone' => 'HP Ayah',
            'mother_name' => 'Nama Ibu',
            'mother_status' => 'Status Ibu',
            'mother_nik' => 'NIK Ibu',
            'mother_birth_date' => 'Tgl Lahir Ibu',
            'mother_education' => 'Pendidikan Ibu',
            'mother_job' => 'Pekerjaan Ibu',
            'mother_income' => 'Penghasilan Ibu',
            'mother_phone' => 'HP Ibu',
            'guardian_name' => 'Nama Wali',
            'guardian_nik' => 'NIK Wali',
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
            'status' => 'Status Pendaftaran',
        ];
    }
}
