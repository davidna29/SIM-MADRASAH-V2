<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Data Utama
            'madrasah_name' => 'MTs Al-Ikhlas Mulia',
            'madrasah_nsm' => '11111111',
            'madrasah_npsn' => '12345678',
            'madrasah_jenjang' => 'MTs',
            'madrasah_status' => 'swasta',
            'madrasah_tahun_berdiri' => '2000',

            // Alamat & Lokasi
            'madrasah_jalan' => 'Jl. Pendidikan No. 123',
            'madrasah_desa' => 'Kel. Ilmu',
            'madrasah_kecamatan' => 'Kec. Semangat',
            'madrasah_kabupaten' => 'Kota Cerdas',
            'madrasah_provinsi' => 'Jawa Barat',
            'madrasah_kode_pos' => '40123',
            'madrasah_latitude' => '-6.9175',
            'madrasah_longitude' => '107.6191',

            // Kontak
            'madrasah_phone' => '(022) 1234567',
            'madrasah_email' => 'info@alikhlas.sch.id',
            'madrasah_website' => 'https://alikhlas.sch.id',

            // Legalitas
            'madrasah_sk_pendirian' => '001/SK/2000',
            'madrasah_tgl_sk_pendirian' => '2000-01-15',
            'madrasah_sk_operasional' => '002/SK/2000',

            // Akreditasi & Naungan
            'madrasah_akreditasi' => 'terakreditasi',
            'madrasah_nilai_akreditasi' => 'B',
            'madrasah_naungan' => 'Kementerian Agama',

            // Logo
            'madrasah_logo' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
