<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\TeachingJournal;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JurnalSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::active();

        $matematika = Subject::where('code', 'MAT')->first();
        $assignment = TeacherAssignment::where('academic_year_id', $tahun->id)
            ->where('subject_id', $matematika?->id)
            ->first();

        if (! $assignment) {
            return;
        }

        $samples = [
            ['offset' => 0, 'period' => 1, 'status' => 'terisi', 'materi' => 'Membilang dan menulis bilangan 1 sampai 20'],
            ['offset' => 1, 'period' => 2, 'status' => 'terisi', 'materi' => 'Membandingkan dua kumpulan benda (lebih banyak / lebih sedikit)'],
            ['offset' => 2, 'period' => 1, 'status' => 'draft', 'materi' => 'Penjumlahan dua bilangan tanpa menyimpan'],
        ];

        foreach ($samples as $s) {
            TeachingJournal::firstOrCreate(
                [
                    'teacher_assignment_id' => $assignment->id,
                    'journal_date' => Carbon::today()->subDays($s['offset'])->toDateString(),
                    'period_no' => $s['period'],
                ],
                [
                    'academic_year_id' => $tahun->id,
                    'materi' => $s['materi'],
                    'tujuan' => 'Siswa dapat menghitung dan menyebutkan hasilnya dengan benar.',
                    'metode' => 'Ceramah interaktif dan permainan kartu bilangan',
                    'catatan' => $s['status'] === 'terisi' ? 'Sebagian besar siswa sudah lancar, 3 siswa perlu bimbingan tambahan.' : null,
                    'tindak_lanjut' => $s['status'] === 'terisi' ? 'Latihan soal di rumah halaman 12.' : null,
                    'status' => $s['status'],
                    'recorded_by' => $assignment->user_id,
                ]
            );
        }
    }
}
