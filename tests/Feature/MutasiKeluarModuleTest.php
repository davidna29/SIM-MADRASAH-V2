<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentMutation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiKeluarModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin', 'username' => 'admin']);
        $this->year = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
    }

    protected function makeStudent(string $nis = '251101'): Student
    {
        $person = Person::create(['nik' => '3518'.str_pad((string) $nis, 12, '0', STR_PAD_LEFT), 'name' => 'Budi Pindah', 'gender' => 'L', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => $nis, 'name' => 'Budi Pindah', 'gender' => 'L']);

        StudentEnrollment::create([
            'academic_year_id' => $this->year->id,
            'class_group_id' => ClassGroup::first()->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        return $student;
    }

    protected function payload(int $studentId, array $overrides = []): array
    {
        return array_merge([
            'student_id' => $studentId,
            'tanggal_mutasi' => '2026-08-28',
            'sekolah_tujuan' => 'MTs Negeri 2 Banjarmasin',
            'tujuan_nsm' => '131237010002',
            'tujuan_npsn' => '30300101',
            'alasan_pindah' => 'pindah_ortu',
            'no_surat' => '421.2/001/MTs',
            'keterangan' => 'Mengikuti orang tua pindah tugas.',
        ], $overrides);
    }

    public function test_index_lists_mutations(): void
    {
        $student = $this->makeStudent();
        StudentMutation::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'tanggal_mutasi' => '2026-08-28',
            'sekolah_tujuan' => 'MTs Negeri 2 Banjarmasin',
            'alasan_pindah' => 'pindah_ortu',
        ]);

        $this->actingAs($this->admin)->get(route('mutasi-keluar.index'))
            ->assertOk()
            ->assertSee('Budi Pindah')
            ->assertSee('MTs Negeri 2 Banjarmasin');
    }

    public function test_create_shows_student_picker(): void
    {
        $this->makeStudent();

        $this->actingAs($this->admin)->get(route('mutasi-keluar.create'))
            ->assertOk()
            ->assertSee('Budi Pindah');
    }

    public function test_store_creates_mutation_and_marks_enrollment_keluar(): void
    {
        $student = $this->makeStudent();

        $this->actingAs($this->admin)->post(route('mutasi-keluar.store'), $this->payload($student->id))
            ->assertRedirect();

        $this->assertDatabaseHas('student_mutations', [
            'student_id' => $student->id,
            'sekolah_tujuan' => 'MTs Negeri 2 Banjarmasin',
        ]);

        $enrollment = StudentEnrollment::where('student_id', $student->id)->first();
        $this->assertSame('keluar', $enrollment->status);
    }

    public function test_stored_student_not_listed_on_active_data_siswa(): void
    {
        $student = $this->makeStudent();

        $this->actingAs($this->admin)->post(route('mutasi-keluar.store'), $this->payload($student->id));

        // Siswa dengan enrollment aktif berubah menjadi 'keluar' -> tidak tampil di daftar aktif.
        $this->assertSame('keluar', StudentEnrollment::where('student_id', $student->id)->first()->status);
        $this->assertSame(
            0,
            Student::whereDoesntHave('enrollments', fn ($q) => $q->where('academic_year_id', $this->year->id)->where('status', 'keluar'))->count()
        );

        $this->actingAs($this->admin)->get(route('siswa.index'))
            ->assertOk()
            ->assertSee('Tidak ada siswa yang cocok');
    }

    public function test_store_rejects_student_without_active_enrollment(): void
    {
        $person = Person::create(['nik' => '3518000101010999', 'name' => 'Tanpa Kelas', 'gender' => 'L', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '299999', 'name' => 'Tanpa Kelas', 'gender' => 'L']);

        $this->actingAs($this->admin)->post(route('mutasi-keluar.store'), $this->payload($student->id))
            ->assertSessionHasErrors('student_id');

        $this->assertDatabaseMissing('student_mutations', ['student_id' => $student->id]);
    }

    public function test_store_rejects_duplicate_same_year(): void
    {
        $student = $this->makeStudent();
        StudentMutation::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'tanggal_mutasi' => '2026-08-01',
            'sekolah_tujuan' => 'MTs Negeri 3',
            'alasan_pindah' => 'pindah_alamat',
        ]);

        $this->actingAs($this->admin)->post(route('mutasi-keluar.store'), $this->payload($student->id))
            ->assertSessionHasErrors('student_id');

        $this->assertSame(1, StudentMutation::count());
    }

    public function test_show_displays_detail(): void
    {
        $student = $this->makeStudent();
        $mutation = StudentMutation::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'tanggal_mutasi' => '2026-08-28',
            'sekolah_tujuan' => 'MTs Negeri 2 Banjarmasin',
            'alasan_pindah' => 'pindah_ortu',
            'keterangan' => 'Catatan khusus.',
        ]);

        $this->actingAs($this->admin)->get(route('mutasi-keluar.show', $mutation))
            ->assertOk()
            ->assertSee('MTs Negeri 2 Banjarmasin')
            ->assertSee('Catatan khusus.');
    }

    public function test_update_changes_metadata_without_touching_enrollment(): void
    {
        $student = $this->makeStudent();
        $mutation = StudentMutation::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'tanggal_mutasi' => '2026-08-28',
            'sekolah_tujuan' => 'MTs Negeri 2 Banjarmasin',
            'alasan_pindah' => 'pindah_ortu',
        ]);
        StudentEnrollment::where('student_id', $student->id)->update(['status' => 'keluar']);

        $this->actingAs($this->admin)->put(route('mutasi-keluar.update', $mutation), $this->payload($student->id, ['sekolah_tujuan' => 'SMP Negeri 4', 'alasan_pindah' => 'lainnya']))
            ->assertRedirect();

        $this->assertSame('SMP Negeri 4', $mutation->fresh()->sekolah_tujuan);
        $this->assertSame('lainnya', $mutation->fresh()->alasan_pindah);
        $this->assertSame('keluar', StudentEnrollment::where('student_id', $student->id)->first()->status);
    }

    public function test_destroy_undo_reverts_enrollment_to_aktif(): void
    {
        $student = $this->makeStudent();
        $mutation = StudentMutation::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'tanggal_mutasi' => '2026-08-28',
            'sekolah_tujuan' => 'MTs Negeri 2 Banjarmasin',
            'alasan_pindah' => 'pindah_ortu',
        ]);
        StudentEnrollment::where('student_id', $student->id)->update(['status' => 'keluar']);

        $this->actingAs($this->admin)->delete(route('mutasi-keluar.destroy', $mutation))
            ->assertRedirect();

        $this->assertDatabaseMissing('student_mutations', ['id' => $mutation->id]);
        $this->assertSame('aktif', StudentEnrollment::where('student_id', $student->id)->first()->status);
    }

    public function test_guru_cannot_access_mutasi_keluar(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('mutasi-keluar.index'))->assertForbidden();
    }
}
