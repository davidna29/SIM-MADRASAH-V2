<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\User;
use App\Support\PpdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PpdbToMasterSyncTest extends TestCase
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
            'scanned_kk' => 'https://drive.google.com/file/d/test-kk',
            'imm_hepb' => 'PERNAH',
            'imm_polio' => 'PERNAH',
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
            'father_nik' => '6172010101010011',
            'mother_name' => 'SITI TEST',
            'mother_status' => 'Masih Hidup',
            'mother_nik' => '6172010101010002',
            'mother_birth_date' => '1990-03-20',
            'parent_ownership' => 'Milik Sendiri',
            'parent_address' => 'Jl. Test No. 1',
            'origin_school' => 'TK Harapan',
            'origin_nsm' => '121234567890',
        ], $overrides));
    }

    public function test_accept_copies_full_data_to_master(): void
    {
        $registration = $this->makeRegistration();
        $student = PpdbService::accept($registration);

        $student->refresh();
        $student->load('person');

        // Student -> master
        $this->assertSame('Olah Raga', $student->hobby);
        $this->assertSame(1, $student->child_order);
        $this->assertSame('TK Harapan', $student->origin_school);
        $this->assertSame('Pahandut', $student->person->district);
        $this->assertTrue($student->imm_hepb);
        $this->assertFalse($student->imm_covid);

        // Person -> alamat
        $this->assertSame('Jl. Test No. 1', $student->person->address);
        $this->assertSame('Kalimantan Tengah', $student->person->province);
        $this->assertSame('001', $student->person->rt);

        // Guardian + pivot relation
        $this->assertDatabaseHas('guardians', ['name' => 'BUDI TEST', 'nik' => '6172010101010011']);
        $this->assertDatabaseHas('guardians', ['name' => 'SITI TEST']);
        $this->assertDatabaseHas('guardian_student', ['student_id' => $student->id, 'relation' => 'ayah']);
        $this->assertDatabaseHas('guardian_student', ['student_id' => $student->id, 'relation' => 'ibu']);
    }

    public function test_guardian_dedupe_by_nik(): void
    {
        $registration = $this->makeRegistration();
        $student = PpdbService::accept($registration);

        // Registrasi kedua dengan ayah NIK sama (anak kedua)
        $reg2 = $this->makeRegistration([
            'registration_no' => 'PPDB-2026-002',
            'nik' => '6172010101010101',
            'name' => 'AHMAD KEDUA',
            'father_nik' => '6172010101010011', // sama
            'mother_nik' => '6172010101010202',
        ]);
        $student2 = PpdbService::accept($reg2);

        $countFathers = Guardian::where('nik', '6172010101010011')->count();
        $this->assertSame(1, $countFathers, 'Ayah dengan NIK sama harus satu record');
        $this->assertDatabaseHas('guardian_student', ['student_id' => $student2->id, 'relation' => 'ayah']);
    }

    public function test_ppdb_edit_locked_after_accept(): void
    {
        $registration = $this->makeRegistration();
        PpdbService::accept($registration);

        $this->actingAs($this->admin)->get(route('ppdb.edit', $registration))->assertForbidden();
        $this->actingAs($this->admin)->put(route('ppdb.update', $registration), [
            'name' => 'UBAH NAMA',
            'nik' => $registration->nik,
            'gender' => 'L',
        ])->assertForbidden();
    }

    public function test_accept_button_shows_lock_notice(): void
    {
        $registration = $this->makeRegistration();
        // status submitted -> tombol Terima + modal
        $response = $this->actingAs($this->admin)->get(route('ppdb.show', $registration));
        $response->assertOk();
        $response->assertSee('MENGUNCI seluruh pengeditan di PPDB');
        $response->assertSee('Lanjutkan', false);
    }

    public function test_backfill_master_for_old_accepted(): void
    {
        $registration = $this->makeRegistration();
        $student = PpdbService::accept($registration);
        // Reset sebagian data master lalu backfill untuk memastikan sinkron ulang
        $student->update(['hobby' => null, 'origin_school' => null]);

        Artisan::call('ppdb:backfill-master');

        $student->refresh();
        $this->assertSame('Olah Raga', $student->hobby);
        $this->assertSame('TK Harapan', $student->origin_school);
    }
}
