<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\PpiExamAspectCategory;
use App\Models\PpiExamGroup;
use App\Models\PpiExamHafalanMateri;
use App\Models\PpiExamHafalanScore;
use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamRoom;
use App\Models\PpiExamScore;
use App\Models\Student;
use App\Models\User;
use App\Services\PpiExamScoringService;
use App\Services\PpiExamService;
use Illuminate\Database\Seeder;

class PpiExamSeeder extends Seeder
{
    protected array $categories = [
        // Penguji I
        ['kode' => '1', 'nama' => 'Wudhu', 'penguji_urutan' => 1, 'urutan' => 1, 'items' => [
            'Niat Wudhu', 'Praktik Wudhu', "Do'a Sesudah Wudhu", 'Niat Tayamum',
        ]],
        ['kode' => '2', 'nama' => 'Praktik Shalat', 'penguji_urutan' => 1, 'urutan' => 2, 'items' => [
            'Lafaz azan', 'Lafaz iqamah', "Do'a sesudah azan", "Do'a sesudah iqamah", 'Niat shalat subuh',
            'Niat shalat zuhur', 'Niat shalat asar', 'Niat shalat magrib', 'Niat shalat isya', "Do'a iftitah",
            'Al-fatihah', "Bacaan ruku'", "Bacaan i'tidal", "Do'a Qunut", 'Bacaan sujud',
            'Bacaan duduk antara 2 sujud', 'Bacaan tahiyat awal', 'Bacaan tahiyat akhir', 'Salam',
            "Do'a sebelum salam", 'Wirid / Dzikir Pendek bada shalat', "Do'a selamat",
        ]],
        // Penguji II
        ['kode' => '3', 'nama' => "Tilawatil Qur'an", 'penguji_urutan' => 2, 'urutan' => 3, 'items' => [
            'Makhorijul huruf', 'Hukum Bacaan', 'Kelancaran',
        ]],
        ['kode' => '4', 'nama' => 'Shalat Jenazah', 'penguji_urutan' => 2, 'urutan' => 4, 'items' => [
            'Niat salat Jenazah untuk laki-laki Dewasa', 'Niat salat Jenazah untuk Perempuan Dewasa',
            'Niat Salat Jenazah untuk Anak laki-laki', 'Niat Salat Jenazah Untuk Anak Perempuan',
            'Bacaan Takbir Pertama', 'Bacaan Takbir Kedua', 'Bacaan Takbir Ketiga', 'Bacaan Takbir Keempat',
        ]],
        ['kode' => '5', 'nama' => 'Hafalan Hadis', 'penguji_urutan' => 2, 'urutan' => 5, 'items' => [
            'Hadis tentang amal Shaleh', 'Hadis tentang keutamaan memberi',
        ]],
        // Penguji III
        ['kode' => '6', 'nama' => "Do'a-Do'a Harian", 'penguji_urutan' => 3, 'urutan' => 6, 'items' => [
            "Do'a Senandung Al-Qur'an", "Do'a mau Belajar", "Do'a Mau makan", "Do'a sesudah makan",
            "Do'a masuk WC", "Do'a keluar WC", "Do'a Masuk rumah", "Do'a Keluar rumah",
            "Do'a Mau tidur", "Do'a bangun tidur", "Do'a masuk mesjid", "Do'a Keluar mesjid",
            "Do'a untuk Kedua Orang Tua", 'Niat Puasa Ramadhan', "Do'a Berbuka Puasa", "Do'a bercermin",
            "Do'a Naik Kendaraan Darat", "Do'a Naik Kendaraan Air",
        ]],
        ['kode' => '7', 'nama' => 'Pengetahuan Agama', 'penguji_urutan' => 3, 'urutan' => 7, 'items' => [
            'Rukun islam', 'Rukun iman', 'Rukun wudhu', 'Rukun shalat', 'Shalat Sunnah',
        ]],
    ];

    protected array $hafalanMateri = [
        'Yaasin', 'Al-Waqi\'ah', 'Ad-Dhuha', 'Al-Insyirah', 'At-Tiin', 'Al-`Alaq', 'Al-Qadar', 'Al-Bayyinah',
        'Al-Zalzalah', 'Al-`Adiyat', 'Al-Qari\'ah', 'At-Takasur', 'Al-`Ashr', 'Al-Humazah', 'Al-Fiil',
        'Al-Quraisy', 'Al-Ma`un', 'Al-Kausar', 'Al-Kafirun', 'An-Nasr', 'Al-Lahab', 'Al-Ikhlas', 'Al-Falaq', 'An-Naas',
    ];

    public function run(): void
    {
        $tahun = AcademicYear::active();

        // Tautkan akun guru demo ke data pegawai (Data Guru/Kepegawaian)
        $umar = $this->linkUserToEmployee('guru.umar', 'Umar Hakim');
        $imam = $this->linkUserToEmployee('guru.imam', 'Imam Syafii');
        $nurul = $this->linkUserToEmployee('guru.nurul', 'Nurul Aini');

        // Penguji ruang 2 (contoh guru lain dari GuruMISeeder — tanpa akun login)
        $anas = $this->employeeByName('ANWARI ANAS');
        $ibrahim = $this->employeeByName('IBRAHIM');
        $mely = $this->employeeByName('MELY ASTUTI');

        // Akun demo Kepala Madrasah (role primer) agar skenario read-only bisa dites
        User::firstOrCreate(
            ['username' => 'kepala'],
            ['name' => 'Kepala Madrasah Demo', 'email' => 'kepala@madrasah.sch.id', 'password' => 'password', 'role' => 'kepala_madrasah']
        );

        $periode = PpiExamPeriod::firstOrCreate(
            ['judul' => 'Ujian PPI Kelas VI TP '.$tahun->name, 'academic_year_id' => $tahun->id],
            [
                'tanggal_setoran_mulai' => now()->subDays(10)->toDateString(),
                'tanggal_setoran_selesai' => now()->subDays(3)->toDateString(),
                'tanggal_ujian' => now()->addDays(7)->toDateString(),
                'status' => PpiExamPeriod::BERLANGSUNG,
                'config_locked_at' => now(),
                'bobot_p1' => 25,
                'bobot_p2' => 25,
                'bobot_p3' => 25,
                'bobot_hafalan' => 25,
                'teks_mc' => PpiExamService::DEFAULT_TEKS_MC,
                'teks_ba' => PpiExamService::DEFAULT_TEKS_BA,
            ]
        );

        $this->seedScales($periode);
        $this->seedAspects($periode);
        $this->seedHafalan($periode);

        $room1 = PpiExamRoom::firstOrCreate(
            ['exam_period_id' => $periode->id, 'nama' => 'Ruang 1'],
        );
        $room2 = PpiExamRoom::firstOrCreate(
            ['exam_period_id' => $periode->id, 'nama' => 'Ruang 2'],
        );

        $this->seedExaminers($periode, $room1, [1 => $umar, 2 => $imam, 3 => $nurul]);
        $this->seedExaminers($periode, $room2, [1 => $anas, 2 => $ibrahim, 3 => $mely]);

        $grupA = PpiExamGroup::firstOrCreate(
            ['exam_period_id' => $periode->id, 'nama' => 'Grup A'],
            ['pembimbing_employee_id' => $umar?->id]
        );
        $grupB = PpiExamGroup::firstOrCreate(
            ['exam_period_id' => $periode->id, 'nama' => 'Grup B'],
            ['pembimbing_employee_id' => $imam?->id]
        );

        $students = Student::query()
            ->whereHas('enrollments', function ($q) use ($tahun) {
                $q->where('academic_year_id', $tahun->id)
                    ->where('status', 'aktif')
                    ->whereHas('classGroup', fn ($c) => $c->where('grade_level', 'VI'));
            })
            ->with(['enrollments' => function ($q) use ($tahun) {
                $q->where('academic_year_id', $tahun->id)->where('status', 'aktif')->with('classGroup');
            }])
            ->orderBy('name')
            ->get();

        // Sebar: Bintang & Citra (VI-B) ke Ruang 1/Grup A; Yusuf & Zahra (VI-A) ke Ruang 2/Grup B
        $bintang = $students->firstWhere('name', 'Bintang Ramadhan');
        $citra = $students->firstWhere('name', 'Citra Ayu');
        $yusuf = $students->firstWhere('name', 'Yusuf Maulana');
        $zahra = $students->firstWhere('name', 'Zahra Aulia');

        $no = 0;
        foreach ([$bintang, $citra, $yusuf, $zahra] as $student) {
            if (! $student) {
                continue;
            }
            $no++;
            $room = in_array($student->name, ['Bintang Ramadhan', 'Citra Ayu'], true) ? $room1 : $room2;
            $group = in_array($student->name, ['Bintang Ramadhan', 'Citra Ayu'], true) ? $grupA : $grupB;

            PpiExamParticipant::firstOrCreate(
                ['exam_period_id' => $periode->id, 'student_id' => $student->id],
                [
                    'exam_room_id' => $room->id,
                    'group_id' => $group->id,
                    'class_group_id' => $student->enrollments->first()?->class_group_id,
                    'no_urut' => $no,
                    'status' => 'aktif',
                ]
            );
        }

        // Sampel nilai: Bintang & Citra lengkap (biar Rekap hidup), Yusuf & Zahra kosong (untuk dites)
        if ($umar && $imam && $nurul) {
            $this->seedSampleScores($periode, $bintang, $room1, [1 => 80, 2 => 85, 3 => 90], 88);
            $this->seedSampleScores($periode, $citra, $room1, [1 => 70, 2 => 75, 3 => 78], 80);
        }

        app(PpiExamScoringService::class)->recomputePeriod($periode);

        $this->command?->info('Ujian PPI Kelas VI (TP '.$tahun->name.') siap — login admin/guru.umar/kepala untuk mencoba.');
    }

    protected function seedScales(PpiExamPeriod $periode): void
    {
        $scales = [
            ['predikat' => 'A+', 'min' => 90, 'max' => 100, 'deskripsi' => 'Sangat Baik — penguasaan materi luar biasa', 'tl' => false],
            ['predikat' => 'A', 'min' => 80, 'max' => 89, 'deskripsi' => 'Sangat Baik', 'tl' => false],
            ['predikat' => 'B', 'min' => 70, 'max' => 79, 'deskripsi' => 'Baik', 'tl' => false],
            ['predikat' => 'C', 'min' => 60, 'max' => 69, 'deskripsi' => 'Cukup', 'tl' => false],
            ['predikat' => 'D', 'min' => 0, 'max' => 59, 'deskripsi' => 'Belum Tuntas', 'tl' => true],
        ];

        foreach ($scales as $urutan => $scale) {
            $periode->scales()->firstOrCreate(
                ['urutan' => $urutan + 1],
                [
                    'predikat' => $scale['predikat'],
                    'nilai_min' => $scale['min'],
                    'nilai_max' => $scale['max'],
                    'deskripsi' => $scale['deskripsi'],
                    'is_tidak_lulus' => $scale['tl'],
                ]
            );
        }
    }

    protected function seedAspects(PpiExamPeriod $periode): void
    {
        foreach ($this->categories as $categoryData) {
            $category = PpiExamAspectCategory::firstOrCreate(
                ['exam_period_id' => $periode->id, 'kode' => $categoryData['kode']],
                [
                    'nama' => $categoryData['nama'],
                    'penguji_urutan' => $categoryData['penguji_urutan'],
                    'urutan' => $categoryData['urutan'],
                ]
            );

            foreach ($categoryData['items'] as $i => $itemNama) {
                $category->aspects()->firstOrCreate(
                    ['kode' => (string) ($i + 1)],
                    ['nama' => $itemNama, 'urutan' => $i + 1]
                );
            }
        }
    }

    protected function seedHafalan(PpiExamPeriod $periode): void
    {
        foreach ($this->hafalanMateri as $i => $nama) {
            PpiExamHafalanMateri::firstOrCreate(
                ['exam_period_id' => $periode->id, 'nama' => $nama],
                ['urutan' => $i + 1]
            );
        }
    }

    protected function seedExaminers(PpiExamPeriod $periode, PpiExamRoom $room, array $byUrutan): void
    {
        foreach ($byUrutan as $urutan => $employee) {
            if (! $employee) {
                continue;
            }
            $room->examiners()->firstOrCreate(
                ['urutan' => $urutan],
                ['exam_period_id' => $periode->id, 'employee_id' => $employee->id]
            );
        }
    }

    protected function seedSampleScores(PpiExamPeriod $periode, ?Student $student, PpiExamRoom $room, array $pengujiNilai, int $hafalanNilai): void
    {
        if (! $student) {
            return;
        }

        $participant = $periode->participants()->where('student_id', $student->id)->first();
        if (! $participant) {
            return;
        }

        $categories = $periode->categories()->with('aspects')->get();

        foreach ($pengujiNilai as $urutan => $nilai) {
            $examiner = $room->examiners()->where('urutan', $urutan)->value('employee_id');
            foreach ($categories->where('penguji_urutan', $urutan) as $category) {
                foreach ($category->aspects as $aspect) {
                    PpiExamScore::firstOrCreate(
                        ['participant_id' => $participant->id, 'aspect_id' => $aspect->id],
                        ['nilai' => $nilai, 'examiner_employee_id' => $examiner, 'input_at' => now()]
                    );
                }
            }
        }

        foreach ($periode->hafalanMateri as $materi) {
            $dinilaiOleh = $participant->group?->pembimbing_employee_id;
            PpiExamHafalanScore::firstOrCreate(
                ['participant_id' => $participant->id, 'hafalan_materi_id' => $materi->id],
                ['nilai' => $hafalanNilai, 'dinilai_oleh_employee_id' => $dinilaiOleh, 'tanggal_setor' => now()->toDateString()]
            );
        }
    }

    protected function linkUserToEmployee(string $username, string $namePart): ?Employee
    {
        $user = User::where('username', $username)->first();

        if (! $user) {
            return null;
        }

        $employee = Employee::query()
            ->whereHas('person', fn ($p) => $p->where('name', 'like', "%{$namePart}%"))
            ->first();

        if ($employee && ! $employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }

        return $employee;
    }

    protected function employeeByName(string $namePart): ?Employee
    {
        return Employee::query()
            ->whereHas('person', fn ($p) => $p->where('name', 'like', "%{$namePart}%"))
            ->first();
    }
}
