<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Person;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\User;
use App\Support\PpdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PpdbProfileSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->admin = User::factory()->create(['role' => 'super_admin', 'username' => 'admin']);
    }

    protected function makeRegistration(array $overrides = []): PpdbRegistration
    {
        return PpdbRegistration::create(array_merge([
            'registration_no' => 'PPDB-2026-001',
            'status' => 'submitted',
            'name' => 'AHMAD TEST',
            'nik' => '6172010101010001',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_place' => 'Palangka Raya',
            'birth_date' => '2018-05-15',
            'hobby' => 'Olah Raga',
            'ambition' => 'PNS',
            'child_order' => 1,
            'sibling_count' => 2,
            'ever_tk' => 'PERNAH',
            'ever_paud' => 'TIDAK',
            'scanned_kk' => 'https://drive.google.com/file/d/test-kk',
            'scanned_akta' => 'https://drive.google.com/file/d/test-akta',
            'imm_hepb' => 'PERNAH',
            'imm_polio' => 'PERNAH',
            'imm_bcg' => 'PERNAH',
            'imm_campak' => 'PERNAH',
            'imm_dpt' => 'PERNAH',
            'imm_covid' => 'TIDAK',
            'residence_type' => 'Tinggal dgn Ortu/Wali',
            'address' => 'Jl. Test No. 1',
            'province' => 'Kalimantan Tengah',
            'city' => 'Palangka Raya',
            'district' => 'Pahandut',
            'village' => 'Pahandut',
            'rt' => '001',
            'rw' => '001',
            'postal_code' => '73111',
            'distance' => '<5km',
            'transport' => 'Sepeda Motor',
            'commute_time' => '10-19 menit',
            'kk_number' => '6172010101010000',
            'kk_head_name' => 'BUDI TEST',
            'father_name' => 'BUDI TEST',
            'father_status' => 'Masih Hidup',
            'mother_name' => 'SITI TEST',
            'mother_status' => 'Masih Hidup',
            'mother_nik' => '6172010101010002',
            'mother_birth_date' => '1990-03-20',
            'mother_education' => '7',
            'mother_job' => '05',
            'mother_income' => 'Rp2jt-3jt',
            'parent_ownership' => 'Milik Sendiri',
            'parent_address' => 'Jl. Test No. 1',
            'parent_province' => 'Kalimantan Tengah',
            'parent_city' => 'Palangka Raya',
            'parent_district' => 'Pahandut',
            'parent_village' => 'Pahandut',
            'parent_rt' => '001',
            'parent_rw' => '001',
            'parent_postal_code' => '73111',
            'previous_school' => 'TK Test',
            'origin_school' => 'TK Harapan',
            'origin_nsm' => '121234567890',
            'origin_address' => 'Jl. Sekolah No. 2',
        ], $overrides));
    }

    public function test_accept_writes_full_profile_snapshot(): void
    {
        $registration = $this->makeRegistration();
        $student = PpdbService::accept($registration);

        $profile = StudentProfile::where('student_id', $student->id)->first();
        $this->assertNotNull($profile);
        $this->assertSame($registration->id, $profile->source_registration_id);

        // Identitas tambahan & asal
        $this->assertSame('Olah Raga', $profile->hobby);
        $this->assertSame(1, $profile->child_order);
        $this->assertSame('TK Test', $profile->previous_school);
        $this->assertSame('TK Harapan', $profile->origin_school);
        $this->assertSame('121234567890', $profile->origin_nsm);

        // Alamat siswa
        $this->assertSame('Jl. Test No. 1', $profile->address);
        $this->assertSame('Pahandut', $profile->district);
        $this->assertSame('001', $profile->rt);

        // Keluarga
        $this->assertSame('BUDI TEST', $profile->father_name);
        $this->assertSame('SITI TEST', $profile->mother_name);
        $this->assertSame('6172010101010002', $profile->mother_nik);

        // Imunisasi: PERNAH -> true, TIDAK -> false
        $this->assertTrue($profile->imm_hepb);
        $this->assertFalse($profile->imm_covid);

        // Dokumen (JSON)
        $this->assertSame('https://drive.google.com/file/d/test-kk', $profile->documents['kk']);
        $this->assertSame('https://drive.google.com/file/d/test-akta', $profile->documents['akta']);

        // Umum: tidak hilang dari registrasi & student ter-recognized
        $this->assertSame($student->id, $registration->fresh()->student_id);
        $this->assertSame('accepted', $registration->fresh()->status);
    }

    public function test_sync_is_idempotent_per_student(): void
    {
        $registration = $this->makeRegistration();
        $student = PpdbService::accept($registration);

        // Simulasi ulang (accept lagi tidak mungkin, tapi backfill/update berulang harus 1 baris)
        StudentProfile::syncFromRegistration($student, $registration);

        $this->assertSame(1, StudentProfile::where('student_id', $student->id)->count());
    }

    public function test_backfill_creates_profiles_for_existing_accepted(): void
    {
        // Registrasi lama yang sudah accepted (tanpa profile) — seakan data sebelum fitur ini
        $registration = $this->makeRegistration();
        $person = Person::create([
            'nik' => $registration->nik, 'name' => $registration->name,
            'gender' => $registration->gender, 'religion' => $registration->religion,
        ]);
        $student = Student::create([
            'person_id' => $person->id, 'nis' => null,
            'name' => $registration->name, 'gender' => $registration->gender,
        ]);
        $registration->update(['status' => 'accepted', 'student_id' => $student->id]);

        Artisan::call('ppdb:backfill-profiles');

        $profile = StudentProfile::where('student_id', $student->id)->first();
        $this->assertNotNull($profile);
        $this->assertSame($registration->id, $profile->source_registration_id);
        $this->assertSame('SITI TEST', $profile->mother_name);
    }

    public function test_student_show_page_shows_full_data_panel(): void
    {
        $registration = $this->makeRegistration();
        $student = PpdbService::accept($registration);

        $response = $this->actingAs($this->admin)->get(route('siswa.show', $student));

        $response->assertOk();
        $response->assertSee('Data Lengkap');
        $response->assertSee('BUDI TEST');
        $response->assertSee($registration->registration_no);
    }

    public function test_manual_student_without_ppdb_has_no_profile_and_page_ok(): void
    {
        $person = Person::create(['nik' => '6172010101010999', 'name' => 'Manual Siswa', 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '250999', 'name' => 'Manual Siswa', 'gender' => 'P']);

        $this->assertNull($student->profile);

        $response = $this->actingAs($this->admin)->get(route('siswa.show', $student));
        $response->assertOk();
    }
}
