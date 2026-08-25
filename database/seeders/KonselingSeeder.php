<?php

namespace Database\Seeders;

use App\Models\CounselingSession;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class KonselingSeeder extends Seeder
{
    public function run(): void
    {
        $guruBk = User::where('username', 'guru.umar')->first();
        if (! $guruBk) {
            return;
        }

        $enrollments = StudentEnrollment::with('student')
            ->where('status', 'aktif')
            ->limit(3)
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        CounselingSession::create([
            'student_enrollment_id' => $enrollments[0]->id,
            'counselor_user_id' => $guruBk->id,
            'session_date' => now()->subDays(10),
            'counseling_type' => 'individual',
            'topic' => 'Motivasi Belajar',
            'problem_description' => 'Siswa menunjukkan penurunan motivasi belajar dalam 2 bulan terakhir. Nilai merata turun di beberapa mata pelajaran.',
            'assessment_result' => 'Siswa mengalami kesulitan fokus akibat masalah di rumah. Perlu pendekatan emosional dan akademik.',
            'action_taken' => 'Melakukan sesi konseling individu, berdiskusi dengan wali kelas, dan menghubungi orang tua.',
            'follow_up_plan' => 'Pemantauan mingguan selama 1 bulan. Evaluasi perkembangan siswa.',
            'confidentiality_level' => 'plus_wali_kelas',
            'status' => 'aktif',
        ]);

        CounselingSession::create([
            'student_enrollment_id' => $enrollments[1]->id,
            'counselor_user_id' => $guruBk->id,
            'session_date' => now()->subDays(5),
            'counseling_type' => 'individual',
            'topic' => 'Konflik Antar Teman',
            'problem_description' => 'Siswa terlibat konflik dengan teman sekelasnya. Dampak: siswa tidak nyaman di kelas.',
            'assessment_result' => null,
            'action_taken' => 'Mediasi antara kedua siswa. Penjelasan tentang pentingnya komunikasi.',
            'follow_up_plan' => 'Pemantauan selama 2 minggu.',
            'confidentiality_level' => 'plus_kepala',
            'status' => 'aktif',
        ]);

        CounselingSession::create([
            'student_enrollment_id' => $enrollments[2]->id,
            'counselor_user_id' => $guruBk->id,
            'session_date' => now()->subDays(20),
            'counseling_type' => 'kelompok',
            'topic' => 'Persiapan Ujian',
            'problem_description' => 'Sesi kelompok untuk siswa kelas VI tentang strategi menghadapi ujian.',
            'assessment_result' => 'Sebagian besar siswa sudah siap. 3 siswa perlu pendampingan khusus.',
            'action_taken' => 'Memberikan tips belajar efektif dan jadwal belajar mandiri.',
            'follow_up_plan' => null,
            'confidentiality_level' => 'guru_bk_only',
            'status' => 'ditutup',
        ]);
    }
}
