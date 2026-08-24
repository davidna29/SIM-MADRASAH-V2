<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class TuitionSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();

        $bendahara = User::firstOrCreate(
            ['username' => 'bendahara'],
            [
                'name' => 'Ibu Fitri Bendahara',
                'email' => 'bendahara@madrasah.sch.id',
                'password' => 'password',
                'role' => 'bendahara',
            ]
        );

        TuitionSetting::firstOrCreate(
            ['academic_year_id' => $tahun->id],
            ['nominal' => 100000]
        );

        // Contoh pembayaran: Aisyah (NIS 240101) lunas bulan Juli & Agustus
        $enrollment = StudentEnrollment::with('student')
            ->where('academic_year_id', $tahun->id)
            ->whereHas('student', fn ($q) => $q->where('nis', '240101'))
            ->first();

        if ($enrollment) {
            foreach ([7, 8] as $bulan) {
                TuitionPayment::firstOrCreate(
                    [
                        'student_enrollment_id' => $enrollment->id,
                        'academic_year_id' => $tahun->id,
                        'bulan' => $bulan,
                    ],
                    [
                        'semester' => $tahun->semester,
                        'nominal' => 100000,
                        'status' => 'lunas',
                        'tanggal_bayar' => now()->setDay(5)->toDateString(),
                        'metode' => 'tunai',
                        'recorded_by' => $bendahara->id,
                    ]
                );
            }
        }
    }
}
