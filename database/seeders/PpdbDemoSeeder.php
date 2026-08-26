<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\PpdbRegistration;
use Illuminate\Database\Seeder;

class PpdbDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();

        $samples = [
            [
                'name' => 'AHMAD RIZKY PRATAMA',
                'nik' => '6172010101010001',
                'gender' => 'L',
                'birth_place' => 'Palangka Raya',
                'birth_date' => '2018-05-15',
                'father_name' => 'BUDI PRATAMA',
                'father_status' => 'Masih Hidup',
                'mother_name' => 'SITI RAHMAWATI',
                'mother_status' => 'Masih Hidup',
                'mother_nik' => '6172010101010002',
                'mother_birth_date' => '1990-03-20',
                'mother_education' => '7',
                'mother_job' => '05',
                'mother_income' => 'Rp2jt-3jt',
                'kk_number' => '6172010101010000',
                'kk_head_name' => 'BUDI PRATAMA',
                'address' => 'Jl. Merdeka No. 10',
                'district' => 'Pahandut',
                'village' => 'Pahandut',
                'rt' => '001',
                'rw' => '001',
                'postal_code' => '73111',
                'residence_type' => 'Tinggal dgn Ortu/Wali',
                'origin_school' => 'TK Harapan Bunda',
                'status' => 'submitted',
            ],
            [
                'name' => 'SITI NURHALIZA',
                'nik' => '6172010101010003',
                'gender' => 'P',
                'birth_place' => 'Palangka Raya',
                'birth_date' => '2018-08-22',
                'father_name' => 'HASANUDIN',
                'father_status' => 'Masih Hidup',
                'mother_name' => 'NURUL Hidayah',
                'mother_status' => 'Masih Hidup',
                'mother_nik' => '6172010101010004',
                'mother_birth_date' => '1991-07-10',
                'mother_education' => '3',
                'mother_job' => '06',
                'mother_income' => 'Rp1jt-2jt',
                'kk_number' => '6172010101010005',
                'kk_head_name' => 'HASANUDIN',
                'address' => 'Jl. Pahlawan No. 5',
                'district' => 'Bukit Batu',
                'village' => 'Bukit Batu',
                'rt' => '002',
                'rw' => '003',
                'postal_code' => '73121',
                'residence_type' => 'Tinggal dgn Ortu/Wali',
                'origin_school' => 'TK Miftahul Jannah',
                'status' => 'submitted',
            ],
            [
                // Data lengkap — tidak ada field kosong (kecuali admin-only)
                'name' => 'MUHAMMAD FARHAN RAMADHAN',
                'nik' => '6172010101010011',
                'nisn' => '0012345678',
                'gender' => 'L',
                'birth_place' => 'PALANGKA RAYA',
                'birth_date' => '2018-03-14',
                'previous_school' => 'TK Islam Al-Falah',
                'hobby' => 'Membaca',
                'ambition' => 'Guru/Dosen',
                'child_order' => 2,
                'sibling_count' => 3,
                'ever_tk' => 'PERNAH',
                'ever_paud' => 'PERNAH',
                'entry_date' => '2026-07-13',
                'scanned_kk' => 'https://drive.google.com/file/d/kk-farhan',
                'scanned_kk_wali' => 'https://drive.google.com/file/d/kk-wali-farhan',
                'scanned_akta' => 'https://drive.google.com/file/d/akta-farhan',
                'scanned_ijazah' => 'https://drive.google.com/file/d/ijazah-farhan',
                'scanned_photo' => 'https://drive.google.com/file/d/foto-farhan',
                'imm_hepb' => 'PERNAH',
                'imm_polio' => 'PERNAH',
                'imm_bcg' => 'PERNAH',
                'imm_campak' => 'PERNAH',
                'imm_dpt' => 'PERNAH',
                'imm_covid' => 'TIDAK',
                'dis_deaf' => 0,
                'dis_blind' => 0,
                'dis_disabled' => 0,
                'dis_intellectual' => 0,
                'dis_behavioral' => 0,
                'dis_slow_learner' => 0,
                'dis_communication' => 0,
                'dis_gifted' => 0,
                'residence_type' => 'Tinggal dgn Ortu/Wali',
                'address' => 'Jl. Rajawali No. 45 RT.002 RW.004',
                'district' => 'Jekan Raya',
                'village' => 'MENTENG',
                'rt' => '002',
                'rw' => '004',
                'postal_code' => '73112',
                'distance' => '5-10km',
                'transport' => 'Antar Jemput Sekolah',
                'commute_time' => '20-29 menit',
                'home_phone' => '0513-4567890',
                'student_phone' => '081234567890',
                'student_email' => 'farhan@gmail.com',
                'kk_number' => '6172010101010012',
                'kk_head_name' => 'ABDUL RAHMAN',
                'father_name' => 'ABDUL RAHMAN',
                'father_status' => 'Masih Hidup',
                'father_nik' => '6172010101010013',
                'father_birth_place' => 'BANJARMASIN',
                'father_birth_date' => '1982-05-20',
                'father_education' => '7',
                'father_job' => '03',
                'father_income' => 'Rp3jt – 5jt',
                'father_phone' => '081255556666',
                'mother_name' => 'NURHAYATI',
                'mother_status' => 'Masih Hidup',
                'mother_nik' => '6172010101010014',
                'mother_birth_place' => 'PALANGKA RAYA',
                'mother_birth_date' => '1985-09-12',
                'mother_education' => '3',
                'mother_job' => '07',
                'mother_income' => 'Rp1jt – 2jt',
                'mother_phone' => '081277778888',
                'guardian_name' => 'H. MOHAMMAD YUSUF',
                'guardian_nik' => '6172010101010015',
                'guardian_birth_place' => 'BANJARMASIN',
                'guardian_birth_date' => '1965-11-02',
                'guardian_education' => '2',
                'guardian_job' => '12',
                'guardian_income' => 'Rp2jt – 3jt',
                'guardian_phone' => '081399991111',
                'social_kks' => '6172010101010016',
                'social_pkh' => 'PKH-2026-001',
                'social_kip' => 'KIP-2026-0001',
                'parent_ownership' => 'Milik Sendiri',
                'parent_address' => 'Jl. Rajawali No. 45 RT.002 RW.004',
                'parent_district' => 'Jekan Raya',
                'parent_village' => 'MENTENG',
                'parent_rt' => '002',
                'parent_rw' => '004',
                'parent_postal_code' => '73112',
                'origin_school' => 'TK Islam Al-Falah',
                'origin_nsm' => '111262710006',
                'origin_npsn' => '00112233',
                'origin_address' => 'Jl. Pendidikan No. 10, Palangka Raya',
                'status' => 'submitted',
            ],
        ];

        foreach ($samples as $data) {
            $data['registration_no'] = PpdbRegistration::generateRegistrationNo();
            // Default hanya dipakai jika belum di-set eksplisit di data sample
            $data['religion'] ??= 'Islam';
            $data['ever_tk'] ??= 'PERNAH';
            $data['ever_paud'] ??= 'TIDAK';
            $data['hobby'] ??= 'Olah Raga';
            $data['ambition'] ??= 'PNS';
            $data['child_order'] ??= 1;
            $data['sibling_count'] ??= 2;
            $data['imm_hepb'] ??= 'PERNAH';
            $data['imm_polio'] ??= 'PERNAH';
            $data['imm_bcg'] ??= 'PERNAH';
            $data['imm_campak'] ??= 'PERNAH';
            $data['imm_dpt'] ??= 'PERNAH';
            $data['imm_covid'] ??= 'TIDAK';
            $data['province'] ??= 'Kalimantan Tengah';
            $data['city'] ??= 'Palangka Raya';
            $data['distance'] ??= '<5km';
            $data['transport'] ??= 'Sepeda Motor';
            $data['commute_time'] ??= '10-19 menit';
            $data['parent_ownership'] ??= 'Milik Sendiri';
            $data['parent_address'] ??= $data['address'];
            $data['parent_province'] ??= 'Kalimantan Tengah';
            $data['parent_city'] ??= 'Palangka Raya';
            $data['parent_district'] ??= $data['district'];
            $data['parent_village'] ??= $data['village'];
            $data['parent_rt'] ??= $data['rt'];
            $data['parent_rw'] ??= $data['rw'];
            $data['parent_postal_code'] ??= $data['postal_code'];
            $data['scanned_kk'] ??= 'https://drive.google.com/file/d/demo-kk';
            $data['scanned_akta'] ??= 'https://drive.google.com/file/d/demo-akta';
            $data['academic_year_id'] ??= $tahun?->id;
            $data['ip_address'] ??= '127.0.0.1';

            PpdbRegistration::create($data);
        }
    }
}
