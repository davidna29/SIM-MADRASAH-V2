<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class WalkingSkeletonSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $kelas = ClassGroup::firstOrCreate(['name' => 'I-A', 'grade_level' => 'I']);
        $mapel = Subject::create(['code' => 'MAT', 'name' => 'Matematika']);

        // Guru
        $guru = User::create([
            'name' => 'Bapak Umar Hakim',
            'username' => 'guru.umar',
            'email' => 'guru@madrasah.sch.id',
            'password' => 'password',
            'role' => 'guru',
        ]);

        TeacherAssignment::create([
            'academic_year_id' => $tahun->id,
            'class_group_id' => $kelas->id,
            'subject_id' => $mapel->id,
            'user_id' => $guru->id,
        ]);

        // Siswa
        $siswaData = [
            ['nis' => '240101', 'name' => 'Aisyah Nur Azizah', 'gender' => 'P'],
            ['nis' => '240102', 'name' => 'Bilal Ramadhan', 'gender' => 'L'],
            ['nis' => '240103', 'name' => 'Cinta Lestari Putri', 'gender' => 'P'],
        ];

        $enrollments = [];
        foreach ($siswaData as $data) {
            $siswa = Student::create($data);
            $enrollments[$data['nis']] = StudentEnrollment::create([
                'academic_year_id' => $tahun->id,
                'class_group_id' => $kelas->id,
                'student_id' => $siswa->id,
                'status' => 'aktif',
            ]);
        }

        // Nilai awal (sebagian terisi agar halaman input terlihat hidup)
        Score::create(['student_enrollment_id' => $enrollments['240101']->id, 'subject_id' => $mapel->id, 'academic_year_id' => $tahun->id, 'semester' => 'ganjil', 'score' => 88]);
        Score::create(['student_enrollment_id' => $enrollments['240102']->id, 'subject_id' => $mapel->id, 'academic_year_id' => $tahun->id, 'semester' => 'ganjil', 'score' => 76]);

        // Orang tua (ibu Aisyah)
        $ibu = User::create([
            'name' => 'Ibu Ratna Dewi',
            'username' => 'ibu.aisy',
            'email' => 'ortu@madrasah.sch.id',
            'password' => 'password',
            'role' => 'orang_tua',
        ]);

        $guardian = Guardian::create(['user_id' => $ibu->id, 'name' => 'Ibu Ratna Dewi']);
        $guardian->students()->attach(Student::where('nis', '240101')->first());

        // Super admin demo
        User::updateOrCreate(
            ['username' => 'admin'],
            ['name' => 'Admin Madrasah', 'email' => 'admin@madrasah.sch.id', 'password' => 'password', 'role' => 'super_admin']
        );

        // Guru tambahan (untuk penugasan mengajar)
        $guruImam = User::firstOrCreate(
            ['username' => 'guru.imam'],
            ['name' => 'Imam Syafii, S.Pd.', 'email' => 'guru.imam2@madrasah.sch.id', 'password' => 'password', 'role' => 'guru']
        );
        $guruNurul = User::firstOrCreate(
            ['username' => 'guru.nurul'],
            ['name' => 'Nurul Aini, S.Pd.', 'email' => 'guru.nurul@madrasah.sch.id', 'password' => 'password', 'role' => 'guru']
        );

        // Akun siswa demo — terhubung ke Aisyah (NIS 240101) untuk Portal Siswa
        $siswaAisy = Student::where('nis', '240101')->first();
        if ($siswaAisy) {
            User::firstOrCreate(
                ['username' => 'siswa.aisy'],
                [
                    'name' => $siswaAisy->name,
                    'email' => 'siswa.aisy@madrasah.sch.id',
                    'password' => 'password',
                    'role' => 'siswa',
                    'student_id' => $siswaAisy->id,
                ]
            );
        }
    }
}
