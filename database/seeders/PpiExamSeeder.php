<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\PpiExamGroup;
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
    public function run(): void
    {
        $tahun = AcademicYear::active();

        $umar = $this->linkUserToEmployee('guru.umar', 'Umar Hakim');
        $imam = $this->linkUserToEmployee('guru.imam', 'Imam Syafii');
        $nurul = $this->linkUserToEmployee('guru.nurul', 'Nurul Aini');

        $anas = $this->employeeByName('ANWARI ANAS');
        $ibrahim = $this->employeeByName('IBRAHIM');
        $mely = $this->employeeByName('MELY ASTUTI');

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
        app(PpiExamService::class)->seedDefaults($periode);

        $room1 = PpiExamRoom::firstOrCreate(['exam_period_id' => $periode->id, 'nama' => 'Ruang 1']);
        $room2 = PpiExamRoom::firstOrCreate(['exam_period_id' => $periode->id, 'nama' => 'Ruang 2']);

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
