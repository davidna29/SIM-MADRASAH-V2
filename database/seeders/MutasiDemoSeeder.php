<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\MutasiRegistration;
use Illuminate\Database\Seeder;

class MutasiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();

        $samples = [
            [
                'name' => 'RAIHAN PUTRA RAMADHAN',
                'nik' => '6172010101010021',
                'nisn' => '0012345679',
                'nis_asal' => 'MTs.2024.015',
                'gender' => 'L',
                'religion' => 'Islam',
                'birth_place' => 'Banjarmasin',
                'birth_date' => '2016-04-10',
                'origin_school' => 'MTs Negeri 1 Banjarmasin',
                'origin_nsm' => '111262710007',
                'origin_npsn' => '00112234',
                'origin_address' => 'Jl. Perintis No. 12, Banjarmasin',
                'kelas_asal' => 'VIII-A',
                'kelas_tujuan' => 'VIII-A',
                'alasan_pindah' => 'Mengikuti orang tua yang bertugas di Palangka Raya.',
                'tanggal_mutasi' => '2026-07-20',
                'address' => 'Jl. RTA Milono No. 9',
                'province' => 'Kalimantan Tengah',
                'city' => 'Palangka Raya',
                'district' => 'Jekan Raya',
                'village' => 'Palangka',
                'rt' => '001',
                'rw' => '002',
                'postal_code' => '73112',
                'student_phone' => '085234567890',
                'student_email' => 'raihan@gmail.com',
                'father_name' => 'HENDRA GUNAWAN',
                'father_nik' => '6172010101010022',
                'father_job' => 'PNS',
                'father_phone' => '081211112222',
                'mother_name' => 'DEWI LESTARI',
                'mother_nik' => '6172010101010023',
                'mother_job' => 'Ibu Rumah Tangga',
                'mother_phone' => '081233334444',
                'scanned_rekomendasi' => 'https://drive.google.com/file/d/rekomendasi-raihan',
                'scanned_rapor' => 'https://drive.google.com/file/d/rapor-raihan',
                'scanned_kk' => 'https://drive.google.com/file/d/kk-raihan',
                'scanned_akta' => 'https://drive.google.com/file/d/akta-raihan',
                'scanned_photo' => 'https://drive.google.com/file/d/foto-raihan',
                'status' => 'submitted',
            ],
            [
                'name' => 'NUR HIKMAH',
                'nik' => '6172010101010031',
                'gender' => 'P',
                'religion' => 'Islam',
                'birth_place' => 'Palangka Raya',
                'birth_date' => '2017-02-14',
                'origin_school' => 'MIS Al-Ikhlas Mulia',
                'kelas_asal' => 'VII-A',
                'kelas_tujuan' => 'VII-A',
                'alasan_pindah' => 'Domisili pindah ke lingkungan madrasah.',
                'address' => 'Jl. Garuda No. 5',
                'province' => 'Kalimantan Tengah',
                'city' => 'Palangka Raya',
                'district' => 'Pahandut',
                'village' => 'Pahandut',
                'rt' => '003',
                'rw' => '001',
                'postal_code' => '73111',
                'student_phone' => '085312345678',
                'father_name' => 'DEDI KURNIAWAN',
                'mother_name' => 'SITI AMINAH',
                'scanned_rekomendasi' => 'https://drive.google.com/file/d/rekomendasi-hikmah',
                'status' => 'submitted',
            ],
            [
                'name' => 'BIMA ARDIANSYAH',
                'nik' => '6172010101010041',
                'gender' => 'L',
                'religion' => 'Islam',
                'birth_place' => 'Sampit',
                'birth_date' => '2015-09-01',
                'origin_school' => 'MTs Darul Ulum Sampit',
                'kelas_asal' => 'IX-B',
                'kelas_tujuan' => 'IX-A',
                'alasan_pindah' => 'Pindah tempat tinggal orang tua.',
                'address' => 'Jl. Beliang No. 3',
                'province' => 'Kalimantan Tengah',
                'city' => 'Palangka Raya',
                'district' => 'Bukit Batu',
                'village' => 'Tangkiling',
                'student_phone' => '085398765432',
                'father_name' => 'SUYONO',
                'mother_name' => 'RATNA SARI',
                'scanned_rekomendasi' => 'https://drive.google.com/file/d/rekomendasi-bima',
                'status' => 'submitted',
            ],
        ];

        foreach ($samples as $data) {
            $data['registration_no'] = MutasiRegistration::generateRegistrationNo();
            $data['academic_year_id'] ??= $tahun?->id;
            $data['ip_address'] ??= '127.0.0.1';

            MutasiRegistration::create($data);
        }
    }
}
