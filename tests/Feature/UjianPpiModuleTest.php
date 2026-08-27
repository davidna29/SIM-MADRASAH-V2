<?php

namespace Tests\Feature;

use App\Exports\PpiExamArchiveTemplateExport;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\Person;
use App\Models\PpiExamHafalanScore;
use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamScore;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\PpiExamScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class UjianPpiModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $kepala;

    protected User $wali;

    protected User $guru1;

    protected User $guru2;

    protected User $guru3;

    protected User $guruTanpaEmployee;

    protected Employee $emp1;

    protected Employee $emp2;

    protected Employee $emp3;

    protected Employee $emp4;

    protected Employee $emp5;

    protected Employee $emp6;

    protected AcademicYear $year;

    protected ClassGroup $kelasVIa;

    protected ClassGroup $kelasVIb;

    /** @var Collection<int, Student> */
    protected Collection $students;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelasVIa = ClassGroup::create(['name' => 'VI-A', 'grade_level' => 'VI']);
        $this->kelasVIb = ClassGroup::create(['name' => 'VI-B', 'grade_level' => 'VI']);

        $names = ['Bintang Ramadhan', 'Citra Ayu', 'Yusuf Maulana', 'Zahra Aulia'];
        $this->students = collect();
        foreach ($names as $i => $name) {
            $person = Person::create(['nik' => '350700000000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT), 'name' => $name, 'gender' => $i % 2 === 0 ? 'L' : 'P', 'religion' => 'Islam']);
            $student = Student::create(['person_id' => $person->id, 'nis' => '26'.str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT), 'name' => $name, 'gender' => $i % 2 === 0 ? 'L' : 'P']);
            StudentEnrollment::create([
                'academic_year_id' => $this->year->id,
                'class_group_id' => $i < 2 ? $this->kelasVIa->id : $this->kelasVIb->id,
                'student_id' => $student->id,
                'status' => 'aktif',
            ]);
            $this->students->push($student);
        }

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kurikulum']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->wali = User::factory()->create(['role' => 'wali_kelas']);

        $this->emp1 = $this->makeEmployee('Umar Hakim', $this->guru1 = User::factory()->create(['role' => 'guru']));
        $this->emp2 = $this->makeEmployee('Imam Syafii', $this->guru2 = User::factory()->create(['role' => 'guru']));
        $this->emp3 = $this->makeEmployee('Nurul Aini', $this->guru3 = User::factory()->create(['role' => 'guru']));
        $this->guruTanpaEmployee = User::factory()->create(['role' => 'guru']);
        $this->emp4 = $this->makeEmployee('Anwar Anas');
        $this->emp5 = $this->makeEmployee('Ibrahim Musa');
        $this->emp6 = $this->makeEmployee('Mely Astuti');
    }

    protected function makeEmployee(string $name, ?User $user = null): Employee
    {
        $person = Person::create(['nik' => '6201'.random_int(100000000000, 999999999999), 'name' => $name, 'gender' => 'L', 'religion' => 'Islam']);

        return Employee::create([
            'person_id' => $person->id,
            'user_id' => $user?->id,
            'employee_status' => 'honor',
            'status' => 'aktif',
        ]);
    }

    /**
     * Periode lengkap siap-uji: skala, aspek (1 kategori/penguji × 2 item), materi,
     * 2 ruang (masing-masing 3 penguji), 2 grup, 4 peserta.
     */
    protected function makePeriod(string $status = 'berlangsung'): PpiExamPeriod
    {
        $period = PpiExamPeriod::create([
            'academic_year_id' => $this->year->id,
            'judul' => 'Ujian PPI TP 2026/2027',
            'tanggal_setoran_mulai' => now()->subDays(3)->toDateString(),
            'tanggal_setoran_selesai' => now()->toDateString(),
            'tanggal_ujian' => now()->addDays(7)->toDateString(),
            'status' => $status,
            'bobot_p1' => 25,
            'bobot_p2' => 25,
            'bobot_p3' => 25,
            'bobot_hafalan' => 25,
        ]);

        $scales = [
            ['predikat' => 'A+', 'min' => 90, 'max' => 100, 'tl' => false],
            ['predikat' => 'A', 'min' => 80, 'max' => 89, 'tl' => false],
            ['predikat' => 'B', 'min' => 70, 'max' => 79, 'tl' => false],
            ['predikat' => 'C', 'min' => 60, 'max' => 69, 'tl' => false],
            ['predikat' => 'D', 'min' => 0, 'max' => 59, 'tl' => true],
        ];
        foreach ($scales as $i => $s) {
            $period->scales()->create([
                'predikat' => $s['predikat'],
                'nilai_min' => $s['min'],
                'nilai_max' => $s['max'],
                'is_tidak_lulus' => $s['tl'],
                'urutan' => $i + 1,
                'deskripsi' => 'Deskripsi '.$s['predikat'],
            ]);
        }

        foreach ([1 => 'Wudhu', 2 => 'Tilawah', 3 => 'Doa Harian'] as $penguji => $nama) {
            $category = $period->categories()->create([
                'kode' => (string) $penguji,
                'nama' => $nama,
                'penguji_urutan' => $penguji,
                'urutan' => $penguji,
            ]);
            for ($j = 1; $j <= 2; $j++) {
                $category->aspects()->create(['kode' => (string) $j, 'nama' => $nama.' Item '.$j, 'urutan' => $j]);
            }
        }

        foreach (['Yaasin', 'Al-Waqiah', 'Ad-Dhuha', 'Al-Ikhlas', 'An-Naas'] as $i => $nama) {
            $period->hafalanMateri()->create(['nama' => $nama, 'urutan' => $i + 1]);
        }

        $room1 = $period->rooms()->create(['nama' => 'Ruang 1']);
        $room2 = $period->rooms()->create(['nama' => 'Ruang 2']);
        foreach ([1 => $this->emp1, 2 => $this->emp2, 3 => $this->emp3] as $urutan => $emp) {
            $room1->examiners()->create(['exam_period_id' => $period->id, 'employee_id' => $emp->id, 'urutan' => $urutan]);
        }
        foreach ([1 => $this->emp4, 2 => $this->emp5, 3 => $this->emp6] as $urutan => $emp) {
            $room2->examiners()->create(['exam_period_id' => $period->id, 'employee_id' => $emp->id, 'urutan' => $urutan]);
        }

        $groupA = $period->groups()->create(['nama' => 'Grup A', 'pembimbing_employee_id' => $this->emp1->id]);
        $groupB = $period->groups()->create(['nama' => 'Grup B', 'pembimbing_employee_id' => $this->emp2->id]);

        foreach ($this->students->values() as $i => $student) {
            $period->participants()->create([
                'student_id' => $student->id,
                'exam_room_id' => $i < 2 ? $room1->id : $room2->id,
                'group_id' => $i < 2 ? $groupA->id : $groupB->id,
                'class_group_id' => $i < 2 ? $this->kelasVIa->id : $this->kelasVIb->id,
                'no_urut' => $i + 1,
                'status' => 'aktif',
            ]);
        }

        if ($status === PpiExamPeriod::BERLANGSUNG) {
            $this->makeBerlangsung($period);
        }

        return $period;
    }

    protected function makeBerlangsung(PpiExamPeriod $period): void
    {
        $period->update(['status' => PpiExamPeriod::BERLANGSUNG, 'config_locked_at' => now()]);
    }

    protected function aspectsOf(PpiExamPeriod $period, int $penguji): Collection
    {
        return $period->categories()
            ->where('penguji_urutan', $penguji)
            ->with('aspects')
            ->get()
            ->flatMap(fn ($c) => $c->aspects);
    }

    // ============================ Periode & transisi ============================

    public function test_super_admin_can_create_period(): void
    {
        $this->actingAs($this->admin)
            ->post(route('ujianppi.periode.store'), [
                'academic_year_id' => $this->year->id,
                'judul' => 'Ujian Baru',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ppi_exam_periods', ['judul' => 'Ujian Baru', 'status' => 'draft']);
    }

    public function test_wali_kelas_cannot_access_periode(): void
    {
        $this->actingAs($this->wali)->get(route('ujianppi.periode.index'))->assertForbidden();
    }

    public function test_period_requires_complete_config_before_berlangsung(): void
    {
        $period = PpiExamPeriod::create([
            'academic_year_id' => $this->year->id,
            'judul' => 'Belum Lengkap',
            'status' => PpiExamPeriod::SETUP,
        ]);

        $this->actingAs($this->admin)
            ->post(route('ujianppi.periode.status', $period), ['status' => 'berlangsung'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('ppi_exam_periods', ['id' => $period->id, 'status' => 'setup']);
    }

    public function test_berlangsung_locks_config_and_wakamad_gets_403(): void
    {
        $period = $this->makePeriod();

        $this->assertNotNull($period->fresh()->config_locked_at);

        $scale = $period->scales()->first();
        $this->actingAs($this->wakamad)
            ->put(route('ujianppi.konfigurasi.skala.update', [$period, $scale]), [
                'predikat' => 'A',
                'nilai_min' => 80,
                'nilai_max' => 89,
                'urutan' => 1,
            ])
            ->assertForbidden();
    }

    public function test_super_admin_unlock_allows_config_change_and_logs(): void
    {
        $period = $this->makePeriod();

        $this->actingAs($this->admin)
            ->post(route('ujianppi.periode.buka-kunci', $period))
            ->assertRedirect();

        $this->assertNull($period->fresh()->config_locked_at);

        $scale = $period->scales()->first();
        $this->actingAs($this->admin)
            ->put(route('ujianppi.konfigurasi.skala.update', [$period, $scale]), [
                'predikat' => 'A+',
                'nilai_min' => 90,
                'nilai_max' => 100,
                'urutan' => 1,
            ])
            ->assertRedirect(route('ujianppi.konfigurasi.skala', $period));

        $this->assertDatabaseHas('ppi_exam_predicate_scales', ['id' => $scale->id, 'predikat' => 'A+']);
        $this->assertDatabaseHas('activity_log', ['description' => 'ujian_ppi_konfigurasi_dibuka_kunci']);
    }

    public function test_invalid_transition_rejected(): void
    {
        $period = $this->makePeriod(PpiExamPeriod::DRAFT);

        $this->actingAs($this->admin)
            ->post(route('ujianppi.periode.status', $period), ['status' => 'selesai'])
            ->assertSessionHasErrors('status');
    }

    public function test_berlangsung_cannot_revert_to_setup_with_scores(): void
    {
        $period = $this->makePeriod();
        $participant = $period->participants()->first();
        $aspect = $this->aspectsOf($period, 1)->first();

        PpiExamScore::create([
            'participant_id' => $participant->id,
            'aspect_id' => $aspect->id,
            'nilai' => 80,
            'examiner_employee_id' => $this->emp1->id,
            'input_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->post(route('ujianppi.periode.status', $period), ['status' => 'setup'])
            ->assertSessionHasErrors('status');
    }

    // ============================ Skala & bobot ============================

    public function test_scale_overlap_rejected(): void
    {
        $period = $this->makePeriod(PpiExamPeriod::SETUP);

        $this->actingAs($this->admin)
            ->post(route('ujianppi.konfigurasi.skala.store', $period), [
                'predikat' => 'X',
                'nilai_min' => 85,
                'nilai_max' => 95,
                'urutan' => 99,
            ])
            ->assertStatus(422);
    }

    public function test_copy_scales_from_previous_period(): void
    {
        $source = $this->makePeriod(PpiExamPeriod::SETUP);
        $target = PpiExamPeriod::create([
            'academic_year_id' => $this->year->id,
            'judul' => 'Periode Baru',
            'status' => PpiExamPeriod::SETUP,
        ]);

        $this->actingAs($this->admin)
            ->post(route('ujianppi.periode.salin-skala', $target))
            ->assertRedirect();

        $this->assertSame($source->scales()->count(), $target->scales()->count());
    }

    public function test_bobot_total_must_be_100(): void
    {
        $period = $this->makePeriod(PpiExamPeriod::SETUP);

        $this->actingAs($this->admin)
            ->put(route('ujianppi.konfigurasi.bobot.update', $period), [
                'bobot_p1' => 20,
                'bobot_p2' => 20,
                'bobot_p3' => 20,
                'bobot_hafalan' => 20,
            ])
            ->assertSessionHasErrors('bobot');

        $this->actingAs($this->admin)
            ->put(route('ujianppi.konfigurasi.bobot.update', $period), [
                'bobot_p1' => 25,
                'bobot_p2' => 25,
                'bobot_p3' => 25,
                'bobot_hafalan' => 25,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ppi_exam_periods', ['id' => $period->id, 'bobot_p1' => 25]);
    }

    // ============================ Ruang, grup, peserta ============================

    public function test_examiner_cannot_be_in_two_rooms_same_period(): void
    {
        $period = $this->makePeriod(PpiExamPeriod::SETUP);
        $rooms = $period->rooms()->get();
        $room1 = $rooms[0];
        $room2 = $rooms[1];

        // Guru emp1 sudah di Ruang 1 → coba masukkan ke Ruang 2
        $this->actingAs($this->admin)
            ->put(route('ujianppi.persiapan.ruang.update', [$period, $room2]), [
                'nama' => 'Ruang 2',
                'penguji_1' => $this->emp1->id,
                'penguji_2' => $this->emp4->id,
                'penguji_3' => $this->emp5->id,
            ])
            ->assertSessionHasErrors('penguji');

        $this->assertDatabaseMissing('ppi_exam_examiners', [
            'exam_period_id' => $period->id,
            'exam_room_id' => $room2->id,
            'employee_id' => $this->emp1->id,
        ]);
    }

    public function test_participant_assign_and_no_urut_alphabetic(): void
    {
        $period = $this->makePeriod(PpiExamPeriod::SETUP);
        $period->participants()->delete();

        $room1 = $period->rooms()->first();
        $groupA = $period->groups()->first();

        $ids = $this->students->pluck('id')->all();

        $this->actingAs($this->admin)
            ->post(route('ujianppi.persiapan.peserta.assign', $period), [
                'student_ids' => $ids,
                'exam_room_id' => $room1->id,
                'group_id' => $groupA->id,
            ])
            ->assertRedirect();

        $names = PpiExamParticipant::where('exam_period_id', $period->id)
            ->with('student')->get()->sortBy('no_urut')->pluck('student.name')->values();

        // Abjad: Bintang, Citra, Yusuf, Zahra
        $this->assertSame(['Bintang Ramadhan', 'Citra Ayu', 'Yusuf Maulana', 'Zahra Aulia'], $names->all());

        // Siswa yang sudah ter-assign tidak bisa di-assign lagi
        $this->actingAs($this->admin)
            ->post(route('ujianppi.persiapan.peserta.assign', $period), [
                'student_ids' => [$this->students[0]->id],
                'exam_room_id' => $room1->id,
                'group_id' => $groupA->id,
            ])
            ->assertSessionHasErrors('student_ids');
    }

    // ============================ Input nilai guru ============================

    public function test_guru_penguji_inputs_only_own_room_and_aspects(): void
    {
        $period = $this->makePeriod();
        $participant = $period->participants()->where('exam_room_id', $period->rooms()->first()->id)->first();
        $aspectP1 = $this->aspectsOf($period, 1)->first();
        $aspectP2 = $this->aspectsOf($period, 2)->first();

        $this->actingAs($this->guru1)
            ->post(route('ujianppi.guru.ujian.store', [$period, $participant]), [
                'room' => $participant->exam_room_id,
                'nilai' => [$aspectP1->id => 85, $aspectP2->id => 95],
            ])
            ->assertRedirect();

        // Aspek penguji 1 tersimpan; aspek penguji 2 (bukan jatahnya) diabaikan
        $this->assertDatabaseHas('ppi_exam_scores', ['participant_id' => $participant->id, 'aspect_id' => $aspectP1->id, 'nilai' => 85]);
        $this->assertDatabaseMissing('ppi_exam_scores', ['participant_id' => $participant->id, 'aspect_id' => $aspectP2->id]);
    }

    public function test_guru_penguji_cannot_input_other_room(): void
    {
        $period = $this->makePeriod();
        $room2 = $period->rooms()->get()[1];
        $participant = $period->participants()->where('exam_room_id', $room2->id)->first();
        $aspectP1 = $this->aspectsOf($period, 1)->first();

        $this->actingAs($this->guru1)
            ->post(route('ujianppi.guru.ujian.store', [$period, $participant]), [
                'room' => $room2->id,
                'nilai' => [$aspectP1->id => 90],
            ])
            ->assertForbidden();
    }

    public function test_guru_without_employee_cannot_input(): void
    {
        $period = $this->makePeriod();

        $this->actingAs($this->guruTanpaEmployee)
            ->get(route('ujianppi.guru.ujian', $period))
            ->assertForbidden();
    }

    public function test_pembimbing_inputs_hafalan_only_own_group(): void
    {
        $period = $this->makePeriod();
        $groupA = $period->groups()->first();
        $groupB = $period->groups()->get()[1];
        $participantA = $period->participants()->where('group_id', $groupA->id)->first();
        $participantB = $period->participants()->where('group_id', $groupB->id)->first();
        $materi = $period->hafalanMateri()->first();

        // guru2 = pembimbing Grup B → grup B boleh, grup A ditolak
        $this->actingAs($this->guru2)
            ->post(route('ujianppi.guru.setoran.store', [$period, $participantB]), [
                'group' => $groupB->id,
                'nilai' => [$materi->id => 88],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ppi_exam_hafalan_scores', ['participant_id' => $participantB->id, 'hafalan_materi_id' => $materi->id, 'nilai' => 88]);

        $this->actingAs($this->guru2)
            ->post(route('ujianppi.guru.setoran.store', [$period, $participantA]), [
                'group' => $groupA->id,
                'nilai' => [$materi->id => 80],
            ])
            ->assertForbidden();
    }

    // ============================ Perhitungan otomatis ============================

    public function test_scoring_recompute_nilai_akhir_predikat_lulus_rank(): void
    {
        $period = $this->makePeriod();
        $participant = $period->participants()->first();
        $room = $participant->room;

        // Lengkapi semua aspek & hafalan
        $p1 = $room->examiners()->where('urutan', 1)->value('employee_id');
        $p2 = $room->examiners()->where('urutan', 2)->value('employee_id');
        $p3 = $room->examiners()->where('urutan', 3)->value('employee_id');

        foreach ([1 => 80, 2 => 85, 3 => 90] as $penguji => $nilai) {
            foreach ($this->aspectsOf($period, $penguji) as $aspect) {
                $examinerId = $penguji === 1 ? $p1 : ($penguji === 2 ? $p2 : $p3);
                PpiExamScore::create([
                    'participant_id' => $participant->id,
                    'aspect_id' => $aspect->id,
                    'nilai' => $nilai,
                    'examiner_employee_id' => $examinerId,
                    'input_at' => now(),
                ]);
            }
        }

        $pembimbing = $participant->group->pembimbing_employee_id;
        foreach ($period->hafalanMateri as $materi) {
            PpiExamHafalanScore::create([
                'participant_id' => $participant->id,
                'hafalan_materi_id' => $materi->id,
                'nilai' => 88,
                'dinilai_oleh_employee_id' => $pembimbing,
                'tanggal_setor' => now()->toDateString(),
            ]);
        }

        app(PpiExamScoringService::class)->recomputeParticipant($participant->refresh());

        $fresh = $participant->fresh();
        $this->assertSame(160, $fresh->jumlah_p1);
        $this->assertSame(80.0, (float) $fresh->rata_p1);
        $this->assertSame(85.0, (float) $fresh->rata_p2);
        $this->assertSame(90.0, (float) $fresh->rata_p3);
        $this->assertSame(85.0, (float) $fresh->rata_ujian_lisan);
        $this->assertSame(88.0, (float) $fresh->rata_hafalan);
        $this->assertSame(85.75, (float) $fresh->nilai_akhir); // (80+85+90+88)/4
        $this->assertSame('A', $fresh->predicateScale?->predikat);
        $this->assertTrue((bool) $fresh->status_lulus);
    }

    public function test_ranks_total_and_lokal(): void
    {
        $period = $this->makePeriod();

        // Lengkapi 2 siswa di rombel BERBEDA (Bintang: VI-A, Yusuf: VI-B)
        $all = $period->participants()->get();
        $students = [$all[0], $all[2]];

        foreach ([[80, 80, 80, 80], [70, 70, 70, 70]] as $i => $combo) {
            $participant = $students[$i];
            $room = $participant->room;
            [$r1, $r2, $r3] = collect([1, 2, 3])->map(fn ($u) => $room->examiners()->where('urutan', $u)->value('employee_id'))->all();

            foreach ([1 => $combo[0], 2 => $combo[1], 3 => $combo[2]] as $penguji => $nilai) {
                foreach ($this->aspectsOf($period, $penguji) as $aspect) {
                    PpiExamScore::create([
                        'participant_id' => $participant->id,
                        'aspect_id' => $aspect->id,
                        'nilai' => $nilai,
                        'examiner_employee_id' => [$r1, $r2, $r3][$penguji - 1],
                        'input_at' => now(),
                    ]);
                }
            }
            foreach ($period->hafalanMateri as $materi) {
                PpiExamHafalanScore::create([
                    'participant_id' => $participant->id,
                    'hafalan_materi_id' => $materi->id,
                    'nilai' => $combo[3],
                    'dinilai_oleh_employee_id' => $participant->group->pembimbing_employee_id,
                    'tanggal_setor' => now()->toDateString(),
                ]);
            }
        }

        $service = app(PpiExamScoringService::class);
        $service->recomputePeriod($period);

        $first = $students[0]->fresh();
        $second = $students[1]->fresh();

        // Bintang (VI-A nilai 80) > Yusuf (VI-B nilai 70); masing-masing rank 1 lokal di rombelnya
        $this->assertSame(1, $first->rank_total);
        $this->assertSame(1, $first->rank_lokal);
        $this->assertSame(2, $second->rank_total);
        $this->assertSame(1, $second->rank_lokal);
    }

    // ============================ Rekap & koreksi ============================

    public function test_rekap_renders_for_admin_and_kepala_read_only(): void
    {
        $period = $this->makePeriod();

        $this->actingAs($this->admin)
            ->get(route('ujianppi.rekap.index', ['periode' => $period->id]))
            ->assertOk()
            ->assertSee('Bintang Ramadhan');

        $this->actingAs($this->kepala)
            ->get(route('ujianppi.rekap.index', ['periode' => $period->id]))
            ->assertOk();

        // Kepala tidak bisa koreksi
        $participant = $period->participants()->first();
        $aspect = $this->aspectsOf($period, 1)->first();

        $this->actingAs($this->kepala)
            ->post(route('ujianppi.rekap.koreksi', [$period, $participant]), [
                'nilai' => [$aspect->id => 100],
                'alasan' => 'Koreksi test',
            ])
            ->assertForbidden();
    }

    public function test_koreksi_requires_alasan_and_logs_changes(): void
    {
        $period = $this->makePeriod();
        $participant = $period->participants()->first();
        $aspect = $this->aspectsOf($period, 1)->first();

        // Tanpa alasan → error
        $this->actingAs($this->admin)
            ->post(route('ujianppi.rekap.koreksi', [$period, $participant]), ['nilai' => [$aspect->id => 75]])
            ->assertSessionHasErrors('alasan');

        // Dengan alasan → tersimpan & tercatat
        $this->actingAs($this->admin)
            ->post(route('ujianppi.rekap.koreksi', [$period, $participant]), [
                'nilai' => [$aspect->id => 75],
                'alasan' => 'Salah input penguji, nilai asli 75',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ppi_exam_scores', ['participant_id' => $participant->id, 'aspect_id' => $aspect->id, 'nilai' => 75]);
        $this->assertDatabaseHas('activity_log', ['description' => 'ujian_ppi_koreksi_nilai']);
    }

    public function test_rekap_exports_pdf_and_excel(): void
    {
        $period = $this->makePeriod();

        $this->actingAs($this->admin)
            ->get(route('ujianppi.rekap.pdf', $period))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('ujianppi.rekap.excel', $period))
            ->assertOk();
    }

    // ============================ Teks & berita acara ============================

    public function test_teks_renders_placeholders(): void
    {
        $period = $this->makePeriod();
        $participant = $period->participants()->first();

        $response = $this->actingAs($this->guru1)
            ->get(route('ujianppi.guru.teks', [$period, $participant]));

        $response->assertOk();
        $response->assertSee('TEKS PEMBAWA ACARA');
        $response->assertSee($participant->student->name);
        $response->assertSee('Umar Hakim'); // penguji I = emp1
    }

    public function test_teks_berita_acara_pdf_downloads(): void
    {
        $period = $this->makePeriod();
        $participant = $period->participants()->first();

        $response = $this->actingAs($this->guru1)
            ->get(route('ujianppi.guru.teks.pdf', [$period, $participant]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    // ============================ Arsip ============================

    public function test_arsip_import_preview_and_simpan(): void
    {
        Excel::store(new PpiExamArchiveTemplateExport, 'ujianppi-test.xlsx', 'local');
        $path = storage_path('app/private/ujianppi-test.xlsx');
        $file = new UploadedFile($path, 'arsip.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $this->actingAs($this->admin)
            ->post(route('ujianppi.arsip.preview'), [
                'judul' => 'Arsip TP 2022/2023',
                'academic_year_id' => $this->year->id,
                'file' => $file,
            ])
            ->assertRedirect(route('ujianppi.arsip.previewShow'));

        $this->actingAs($this->admin)
            ->get(route('ujianppi.arsip.previewShow'))
            ->assertOk()
            ->assertSee('Contoh Siswa');

        $this->actingAs($this->admin)
            ->post(route('ujianppi.arsip.simpan'))
            ->assertRedirect(route('ujianppi.arsip.index'));

        $this->assertDatabaseHas('ppi_exam_periods', ['judul' => 'Arsip TP 2022/2023', 'status' => 'diarsipkan']);
        $this->assertDatabaseHas('ppi_exam_archives', ['nama_siswa' => 'Contoh Siswa', 'nilai_akhir' => 88.75]);

        @unlink($path);
    }

    public function test_period_destroy_blocked_when_berlangsung(): void
    {
        $period = $this->makePeriod();

        $this->actingAs($this->admin)
            ->delete(route('ujianppi.periode.destroy', $period))
            ->assertSessionHasErrors('period');

        $this->assertDatabaseHas('ppi_exam_periods', ['id' => $period->id]);
    }

    // ============================ Smoke: semua halaman render ============================

    public function test_all_admin_and_config_pages_render(): void
    {
        $period = $this->makePeriod(PpiExamPeriod::SETUP);

        $routes = [
            route('ujianppi.periode.index'),
            route('ujianppi.periode.show', $period),
            route('ujianppi.konfigurasi.skala', $period),
            route('ujianppi.konfigurasi.bobot', $period),
            route('ujianppi.konfigurasi.aspek', $period),
            route('ujianppi.konfigurasi.hafalan', $period),
            route('ujianppi.persiapan.ruang', $period),
            route('ujianppi.persiapan.grup', $period),
            route('ujianppi.persiapan.peserta', $period),
            route('ujianppi.arsip.index'),
            route('ujianppi.guru.index'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_guru_ujian_setoran_pages_render(): void
    {
        $period = $this->makePeriod();

        $this->actingAs($this->admin)->get(route('ujianppi.guru.ujian', $period))->assertOk();
        $this->actingAs($this->admin)->get(route('ujianppi.guru.setoran', $period))->assertOk();

        $room = $period->rooms()->first();
        $group = $period->groups()->first();
        $this->actingAs($this->guru1)
            ->get(route('ujianppi.guru.ujian', ['periode' => $period, 'room' => $room->id]))
            ->assertOk();
        $this->actingAs($this->guru1)
            ->get(route('ujianppi.guru.setoran', ['periode' => $period, 'group' => $group->id]))
            ->assertOk();
    }
}
