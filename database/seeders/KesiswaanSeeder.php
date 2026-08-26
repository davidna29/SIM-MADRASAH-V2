<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Offense;
use App\Models\PembiasaanMateri;
use App\Models\PembiasaanNilai;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\PembiasaanService;
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

        // Seed master materi PPI & Tahfidz (beserta pemetaan kelas×semester)
        $this->call([PpiMateriSeeder::class, TahfidzMateriSeeder::class]);

        // Demo nilai untuk Aisyah pada kolom berjalan agar tampilan tidak kosong
        $ay = AcademicYear::active();
        if ($ay) {
            $enr = StudentEnrollment::where('student_id', $aisyah->id)
                ->where('academic_year_id', $ay->id)
                ->where('status', 'aktif')
                ->with('classGroup')
                ->first();

            if ($enr && $enr->classGroup) {
                $kelas = PembiasaanService::gradeToInt($enr->classGroup->grade_level);
                $smt = PembiasaanService::SEMESTER_MAP[$ay->semester] ?? 1;

                foreach (['ppi', 'tahfidz'] as $modul) {
                    $mats = PembiasaanMateri::forModul($modul)->with('periodes')->get();
                    $n = 0;
                    foreach ($mats as $m) {
                        if ($n >= 6) {
                            break;
                        }
                        $per = $m->periodes->first(fn ($p) => $p->kelas === $kelas && $p->semester === $smt && $p->aktif);
                        if (! $per) {
                            continue;
                        }
                        PembiasaanNilai::firstOrCreate(
                            ['siswa_id' => $aisyah->id, 'materi_id' => $m->id, 'kelas' => $kelas, 'semester' => $smt],
                            ['nilai' => rand(70, 95), 'tahun_pelajaran' => $ay->name]
                        );
                        $n++;
                    }
                }
            }
        }
    }
}
