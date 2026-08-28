<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentMasterEditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->admin = User::factory()->create(['role' => 'super_admin', 'username' => 'admin']);
    }

    protected function makeStudent(array $overrides = []): Student
    {
        $person = Person::create(array_merge([
            'nik' => '6172010101010001',
            'name' => 'SISWA TES',
            'gender' => 'L',
            'religion' => 'Islam',
        ], $overrides['person'] ?? []));

        return Student::create(array_merge([
            'person_id' => $person->id,
            'nis' => '251001',
            'name' => 'SISWA TES',
            'gender' => 'L',
        ], $overrides['student'] ?? []));
    }

    public function test_update_student_persists_master_profile_and_person_address(): void
    {
        $student = $this->makeStudent();

        $response = $this->actingAs($this->admin)->put(route('siswa.update', $student), [
            'nis' => '251001',
            'nik' => '6172010101010001',
            'name' => 'SISWA TES',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Baru No. 9',
            'city' => 'Palangka Raya',
            'nisn' => '0123456789',
            'hobby' => 'Membaca',
            'child_order' => 2,
            'origin_school' => 'TK Harapan',
            'imm_hepb' => '1',
            'scanned_kk' => 'https://drive.google.com/file/d/x',
        ]);

        $response->assertRedirect();

        $student->refresh();
        $student->load('person');
        $this->assertSame('0123456789', $student->nisn);
        $this->assertSame('Membaca', $student->hobby);
        $this->assertSame(2, $student->child_order);
        $this->assertSame('TK Harapan', $student->origin_school);
        $this->assertTrue($student->imm_hepb);
        $this->assertSame('Jl. Baru No. 9', $student->person->address);
        $this->assertSame('https://drive.google.com/file/d/x', $student->documents['kk']);
    }

    public function test_update_student_creates_guardian_with_relation_when_missing(): void
    {
        $student = $this->makeStudent();

        $this->actingAs($this->admin)->put(route('siswa.update', $student), [
            'nis' => '251001',
            'nik' => '6172010101010001',
            'name' => 'SISWA TES',
            'gender' => 'L',
            'father_name' => 'BAPAK TES',
            'father_nik' => '6172010101010011',
            'mother_name' => 'IBU TES',
        ]);

        $student->refresh();
        $student->load('guardians');

        $ayah = $student->guardianByRelation('ayah');
        $this->assertNotNull($ayah);
        $this->assertSame('BAPAK TES', $ayah->name);
        $this->assertDatabaseHas('guardian_student', ['student_id' => $student->id, 'relation' => 'ayah']);

        $ibu = $student->guardianByRelation('ibu');
        $this->assertSame('IBU TES', $ibu->name);
    }

    public function test_update_student_updates_existing_guardian_via_id(): void
    {
        $student = $this->makeStudent();
        $ayah = Guardian::create(['user_id' => null, 'name' => 'Ayah Lama', 'nik' => '6172010101010011']);
        $student->guardians()->attach($ayah->id, ['relation' => 'ayah']);

        $this->actingAs($this->admin)->put(route('siswa.update', $student), [
            'nis' => '251001',
            'nik' => '6172010101010001',
            'name' => 'SISWA TES',
            'gender' => 'L',
            'father_id' => $ayah->id,
            'father_name' => 'Ayah Baru',
            'father_nik' => '6172010101010011',
        ]);

        $this->assertSame('Ayah Baru', $ayah->fresh()->name);
        $this->assertSame(1, Guardian::where('nik', '6172010101010011')->count());
    }

    public function test_invalid_child_order_rejected(): void
    {
        $student = $this->makeStudent();

        $response = $this->actingAs($this->admin)->put(route('siswa.update', $student), [
            'nis' => '251001',
            'nik' => '6172010101010001',
            'name' => 'SISWA TES',
            'gender' => 'L',
            'child_order' => -3,
        ]);

        $response->assertSessionHasErrors('child_order');
    }

    public function test_show_page_displays_master_data(): void
    {
        $student = $this->makeStudent(['student' => ['hobby' => 'Melukis', 'origin_school' => 'TK Budi']]);
        $ayah = Guardian::create(['user_id' => null, 'name' => 'Bapak Seniman']);
        $student->guardians()->attach($ayah->id, ['relation' => 'ayah']);

        $response = $this->actingAs($this->admin)->get(route('siswa.show', $student));

        $response->assertOk();
        $response->assertSee('Melukis');
        $response->assertSee('Bapak Seniman');
    }
}
