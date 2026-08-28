<?php

namespace App\Support;

use App\Models\Guardian;
use App\Models\Person;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PpdbService
{
    /**
     * Accept a PPDB registration — create Student, Person, Guardian.
     *
     * NIS & kelas TIDAK ditentukan di PPDB: Student dibuat tanpa NIS dan tanpa
     * enrollment. Operator melengkapi NIS & kelas melalui modul Data Siswa.
     */
    public static function accept(PpdbRegistration $registration): Student
    {
        // Guard NIK duplikat: NIK unik per individu. Jika sudah ada Person
        // (siswa/pegawai/calon lain) atau registrasi lain yang diterima dengan
        // NIK yang sama, tolak dengan pesan ramah, bukan 500.
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

            // 2. Create Student (NIS diisi belakangan via Data Siswa)
            $student = Student::create([
                'person_id' => $person->id,
                'nis' => null,
                'name' => strtoupper($registration->name),
                'gender' => $registration->gender,
            ]);

            // 3. Create Guardian
            $guardian = Guardian::create([
                'user_id' => null,
                'name' => $registration->father_name ?? $registration->mother_name ?? $registration->guardian_name ?? '-',
            ]);

            \DB::table('guardian_student')->insert([
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
            ]);

            // 4. Snapshot profil lengkap PPDB -> student (anti data loss)
            StudentProfile::syncFromRegistration($student, $registration);

            // 5. Enrollment created later when class is assigned (class_group_id NOT NULL)

            // 6. Update registration (tanpa NIS)
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
                ->log('PPDB diterima: '.$registration->name.' (NIS & kelas diisi di Data Siswa)');

            return $student;
        });
    }

    /**
     * Export-friendly column mapping for EMIS.
     * Urutan kolom mengikuti urutan field pada formulir PPDB (Langkah 1–7),
     * ditambah 5 kolom link Google Drive dan diakhiri kolom admin (kelas/NIS/status).
     */
    public static function exportMapping(): array
    {
        return [
            // 1. Identitas Siswa
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
            // 2. Dokumen (link Google Drive)
            'scanned_kk' => 'Link KK',
            'scanned_kk_wali' => 'Link KK Wali',
            'scanned_akta' => 'Link Akta',
            'scanned_ijazah' => 'Link Ijazah',
            'scanned_photo' => 'Link Foto',
            // 3. Imunisasi
            'imm_hepb' => 'Imunisasi Hepatitis B',
            'imm_polio' => 'Imunisasi Polio',
            'imm_bcg' => 'Imunisasi BCG',
            'imm_campak' => 'Imunisasi Campak',
            'imm_dpt' => 'Imunisasi DPT-HB-HiB',
            'imm_covid' => 'Vaksin COVID',
            // 4. Berkebutuhan Khusus
            'dis_deaf' => 'Tuna Rungu',
            'dis_blind' => 'Tuna Netra',
            'dis_disabled' => 'Tuna Daksa',
            'dis_intellectual' => 'Tuna Grahita',
            'dis_behavioral' => 'Tuna Laras',
            'dis_slow_learner' => 'Lamban Belajar',
            'dis_communication' => 'Gangguan Komunikasi',
            'dis_gifted' => 'Bakat Luar Biasa',
            // 5. Alamat Siswa
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
            // 6. Orang Tua / Wali
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
            // 7. Alamat Orang Tua
            'parent_ownership' => 'Status Rumah',
            'parent_address' => 'Alamat Orang Tua',
            'parent_province' => 'Provinsi OT',
            'parent_city' => 'Kota OT',
            'parent_district' => 'Kecamatan OT',
            'parent_village' => 'Kelurahan OT',
            'parent_rt' => 'RT OT',
            'parent_rw' => 'RW OT',
            'parent_postal_code' => 'Kode Pos OT',
            // 8. Sekolah Asal
            'origin_school' => 'Sekolah Asal',
            'origin_nsm' => 'NSM Sekolah Asal',
            'origin_npsn' => 'NPSN Sekolah Asal',
            'origin_address' => 'Alamat Sekolah Asal',
            // 9. Admin-only
            'kelas' => 'Kelas',
            'rombel' => 'Rombel',
            'nis_nism' => 'NIS/NISM',
            'nis_last6' => 'NIS 6 Digit',
            'status' => 'Status Pendaftaran',
        ];
    }
}
