<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\ExtracurricularMember;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EkstrakurikulerSeeder extends Seeder
{
    public function run(): void
    {
        $pembina = User::where('username', 'guru.umar')->first();
        $admin = User::where('username', 'admin')->first();

        if (! $pembina) {
            return;
        }

        $pramuka = Extracurricular::firstOrCreate(
            ['slug' => Str::slug('Pramuka')],
            [
                'name' => 'Pramuka',
                'description' => 'Kepramukaan wajib — latihan rutin mingguan.',
                'pembina_id' => $pembina->id,
                'hari' => 'sabtu',
                'waktu' => '14:00',
                'lokasi' => 'Lapangan utama',
                'status' => 'aktif',
                'created_by' => $admin?->id,
            ]
        );

        // Anggota: 3 siswa pertama di kelas I-A
        $tahun = AcademicYear::active();
        $enrollments = StudentEnrollment::with('student')
            ->where('academic_year_id', $tahun->id)
            ->orderBy('id')
            ->take(3)
            ->get();

        foreach ($enrollments as $i => $enrollment) {
            $member = ExtracurricularMember::firstOrCreate(
                [
                    'extracurricular_id' => $pramuka->id,
                    'student_enrollment_id' => $enrollment->id,
                ],
                ['tanggal_bergabung' => now()->subWeeks(4)->toDateString()]
            );

            // Contoh presensi + predikat 2 sesi terakhir
            foreach ([1, 2] as $w) {
                $tanggal = now()->subWeeks($w)->toDateString();

                ExtracurricularAttendance::firstOrCreate(
                    [
                        'extracurricular_id' => $pramuka->id,
                        'student_enrollment_id' => $enrollment->id,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status' => 'hadir',
                        'predikat' => ['A', 'B'][$i % 2],
                        'keterangan' => null,
                    ]
                );
            }
        }
    }
}
