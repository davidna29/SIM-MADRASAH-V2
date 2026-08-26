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
        ];

        foreach ($samples as $data) {
            $data['registration_no'] = PpdbRegistration::generateRegistrationNo();
            $data['religion'] = 'Islam';
            $data['ever_tk'] = 'PERNAH';
            $data['ever_paud'] = 'TIDAK';
            $data['hobby'] = 'Olah Raga';
            $data['ambition'] = 'PNS';
            $data['child_order'] = 1;
            $data['sibling_count'] = 2;
            $data['imm_hepb'] = 'PERNAH';
            $data['imm_polio'] = 'PERNAH';
            $data['imm_bcg'] = 'PERNAH';
            $data['imm_campak'] = 'PERNAH';
            $data['imm_dpt'] = 'PERNAH';
            $data['imm_covid'] = 'TIDAK';
            $data['province'] = 'Kalimantan Tengah';
            $data['city'] = 'Palangka Raya';
            $data['distance'] = '<5km';
            $data['transport'] = 'Sepeda Motor';
            $data['commute_time'] = '10-19 menit';
            $data['parent_ownership'] = 'Milik Sendiri';
            $data['parent_address'] = $data['address'];
            $data['parent_province'] = 'Kalimantan Tengah';
            $data['parent_city'] = 'Palangka Raya';
            $data['parent_district'] = $data['district'];
            $data['parent_village'] = $data['village'];
            $data['parent_rt'] = $data['rt'];
            $data['parent_rw'] = $data['rw'];
            $data['parent_postal_code'] = $data['postal_code'];
            $data['scanned_kk'] = 'https://drive.google.com/file/d/demo-kk';
            $data['scanned_akta'] = 'https://drive.google.com/file/d/demo-akta';
            $data['academic_year_id'] = $tahun?->id;
            $data['ip_address'] = '127.0.0.1';

            PpdbRegistration::create($data);
        }
    }
}
