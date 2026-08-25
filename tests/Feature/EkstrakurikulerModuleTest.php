<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\ExtracurricularMember;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstrakurikulerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $wakamad;

    protected User $pembina;

    protected User $guruLain;

    protected User $kepala;

    protected StudentEnrollment $enrollment;

    protected ClassGroup $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakamad = User::factory()->create(['role' => 'wakamad_kesiswaan']);
        $this->pembina = User::factory()->create(['role' => 'guru', 'name' => 'Pembina A']);
        $this->guruLain = User::factory()->create(['role' => 'guru', 'name' => 'Guru Lain']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $person = Person::create(['nik' => '3525100101', 'name' => 'Aisyah', 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '251001', 'name' => 'Aisyah', 'gender' => 'P']);
        $this->enrollment = StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $this->kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);
    }

    protected function makeEkskul(?User $pembina = null): Extracurricular
    {
        static $counter = 0;
        $counter++;

        return Extracurricular::create([
            'name' => 'Futsal',
            'slug' => 'futsal-'.$counter,
            'pembina_id' => ($pembina ?? $this->pembina)->id,
            'hari' => 'sabtu',
            'lokasi' => 'Lapangan',
            'status' => 'aktif',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_wakamad_can_create_ekskul(): void
    {
        $response = $this->actingAs($this->wakamad)->post(route('ekskul.store'), [
            'name' => 'Pramuka',
            'pembina_id' => $this->pembina->id,
            'hari' => 'sabtu',
            'status' => 'aktif',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('extracurriculars', ['name' => 'Pramuka', 'pembina_id' => $this->pembina->id]);
    }

    public function test_pembina_can_manage_own_but_not_others(): void
    {
        $own = $this->makeEkskul($this->pembina);
        $other = $this->makeEkskul($this->guruLain);

        // Pembina bisa tambah anggota di ekskulnya
        $this->actingAs($this->pembina)->post(route('ekskul.member.store', $own), [
            'student_enrollment_id' => $this->enrollment->id,
        ])->assertSessionHasNoErrors();

        // Pembina tidak bisa mengelola ekskul orang lain
        $this->actingAs($this->pembina)->post(route('ekskul.member.store', $other), [
            'student_enrollment_id' => $this->enrollment->id,
        ])->assertForbidden();

        $this->actingAs($this->pembina)->put(route('ekskul.update', $other), [
            'name' => 'Dibajak',
            'pembina_id' => $this->pembina->id,
            'status' => 'aktif',
        ])->assertForbidden();
    }

    public function test_duplicate_member_is_blocked(): void
    {
        $ekskul = $this->makeEkskul();
        $payload = ['student_enrollment_id' => $this->enrollment->id];

        $this->actingAs($this->wakamad)->post(route('ekskul.member.store', $ekskul), $payload);
        $this->actingAs($this->wakamad)->post(route('ekskul.member.store', $ekskul), $payload)
            ->assertSessionHasErrors('student_enrollment_id');

        $this->assertSame(1, ExtracurricularMember::count());
    }

    public function test_presensi_is_upserted_and_predikat_only_when_hadir(): void
    {
        $ekskul = $this->makeEkskul();
        $eid = $this->enrollment->id;
        $tanggal = now()->toDateString();

        // Tambah anggota terlebih dahulu
        $this->actingAs($this->wakamad)->post(route('ekskul.member.store', $ekskul), [
            'student_enrollment_id' => $eid,
        ]);

        $base = ['tanggal' => $tanggal, 'statuses' => [$eid => ['status' => 'hadir', 'predikat' => 'B']]];

        $this->actingAs($this->wakamad)->post(route('ekskul.presensi', $ekskul), $base);
        $this->assertDatabaseHas('extracurricular_attendances', [
            'student_enrollment_id' => $eid, 'tanggal' => $tanggal, 'status' => 'hadir', 'predikat' => 'B',
        ]);

        // Simpan ulang (update) + alpha dengan predikat → predikat dikosongkan server-side
        $this->actingAs($this->wakamad)->post(route('ekskul.presensi', $ekskul), array_merge($base, [
            'statuses' => [$eid => ['status' => 'alpha', 'predikat' => 'A']],
        ]));

        $this->assertSame(1, ExtracurricularAttendance::count());
        $att = ExtracurricularAttendance::first();
        $this->assertSame('alpha', $att->status);
        $this->assertNull($att->predikat);
    }

    public function test_rekap_average_and_final_predicate(): void
    {
        $ekskul = $this->makeEkskul();
        $eid = $this->enrollment->id;
        $d1 = now()->subWeeks(2)->toDateString();
        $d2 = now()->subWeek()->toDateString();

        foreach ([['A', $d1], ['C', $d2]] as [$pred, $tgl]) {
            ExtracurricularAttendance::create([
                'extracurricular_id' => $ekskul->id,
                'student_enrollment_id' => $eid,
                'tanggal' => $tgl,
                'status' => 'hadir',
                'predikat' => $pred,
            ]);
        }

        // (4 + 2) / 2 = 3.0 → B
        $response = $this->actingAs($this->kepala)->get(route('ekskul.show', $ekskul));

        $response->assertOk();
        $response->assertSee('3');
        $response->assertSee('B');
    }

    public function test_show_renders_with_members(): void
    {
        $ekskul = $this->makeEkskul();
        $this->actingAs($this->admin)->post(route('ekskul.member.store', $ekskul), [
            'student_enrollment_id' => $this->enrollment->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('ekskul.show', $ekskul));

        $response->assertOk();
        $response->assertSee('Aisyah');
        $response->assertSee('Anggota (1)');
    }

    public function test_pembina_sees_management_ui_but_guru_lain_does_not(): void
    {
        $ekskul = $this->makeEkskul($this->pembina);
        $this->actingAs($this->pembina)->post(route('ekskul.member.store', $ekskul), [
            'student_enrollment_id' => $this->enrollment->id,
        ]);

        $pembinaPage = $this->actingAs($this->pembina)->get(route('ekskul.show', $ekskul));
        $pembinaPage->assertOk();
        $pembinaPage->assertSee('Simpan Presensi');
        $pembinaPage->assertSee('Tambah Anggota');
        // Pembina kelola isi, bukan detail -> tidak lihat tombol "Ubah"
        $pembinaPage->assertDontSee(route('ekskul.edit', $ekskul));

        $guruLainPage = $this->actingAs($this->guruLain)->get(route('ekskul.show', $ekskul));
        $guruLainPage->assertOk();
        $guruLainPage->assertDontSee('Simpan Presensi');
        $guruLainPage->assertDontSee('Tambah Anggota');
    }

    public function test_admin_sees_edit_button_but_pembina_does_not(): void
    {
        $ekskul = $this->makeEkskul($this->pembina);

        $this->actingAs($this->admin)->get(route('ekskul.show', $ekskul))
            ->assertOk()
            ->assertSee(route('ekskul.edit', $ekskul));

        $this->actingAs($this->pembina)->get(route('ekskul.show', $ekskul))
            ->assertOk()
            ->assertDontSee(route('ekskul.edit', $ekskul));
    }

    public function test_index_hides_admin_actions_from_guru(): void
    {
        $this->makeEkskul($this->pembina);

        $adminIndex = $this->actingAs($this->admin)->get(route('ekskul.index'));
        $adminIndex->assertOk();
        $adminIndex->assertSee('Tambah Ekskul');

        $guruIndex = $this->actingAs($this->guruLain)->get(route('ekskul.index'));
        $guruIndex->assertOk();
        $guruIndex->assertDontSee('Tambah Ekskul');
        $guruIndex->assertDontSee('Hapus ekskul beserta anggota & presensinya?');
    }

    public function test_role_access(): void
    {
        $ekskul = $this->makeEkskul();

        $this->actingAs($this->kepala)->get(route('ekskul.index'))->assertOk();
        $this->actingAs($this->kepala)->post(route('ekskul.store'), [
            'name' => 'Hadroh',
            'pembina_id' => $this->pembina->id,
            'status' => 'aktif',
        ])->assertForbidden();

        $this->actingAs($this->guruLain)->get(route('ekskul.index'))->assertOk();
        $this->actingAs($this->guruLain)->delete(route('ekskul.destroy', $ekskul))->assertForbidden();
    }
}
