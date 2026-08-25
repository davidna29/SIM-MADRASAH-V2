<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Offense;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class KesiswaanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $aisyah = Student::where('nis', '240101')->first();

        if (! $aisyah) {
            return;
        }

        Achievement::firstOrCreate(
            ['student_id' => $aisyah->id, 'nama_kegiatan' => 'Lomba Pidato Bahasa Arab'],
            [
                'jenis' => 'nonakademik',
                'tingkat' => 'kabupaten',
                'penyelenggara' => 'Kemenag Kabupaten',
                'tanggal' => now()->subMonths(1)->toDateString(),
                'peringkat' => 'Juara 1',
                'pembimbing' => 'Bapak Imam Syafii',
                'status_verifikasi' => 'terverifikasi',
                'status_publikasi' => 'publik',
                'created_by' => $admin?->id,
            ]
        );

        Offense::firstOrCreate(
            ['student_id' => $aisyah->id, 'kategori' => 'Terlambat Masuk'],
            [
                'tingkat' => 'ringan',
                'poin' => 2,
                'tanggal_kejadian' => now()->subWeek()->toDateString(),
                'kronologi' => 'Datang terlambat setelah bel masuk tanpa keterangan.',
                'pelapor' => 'Guru Piket',
                'tindakan' => 'Pembinaan lisan oleh wali kelas.',
                'status_penyelesaian' => 'selesai',
                'created_by' => $admin?->id,
            ]
        );
    }
}
