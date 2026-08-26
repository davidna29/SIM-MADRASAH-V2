<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\PpdbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->guru = User::factory()->create(['role' => 'guru']);
    }

    protected function validData(): array
    {
        return [
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
            'origin_school' => 'TK Test',
        ];
    }

    // === Public Form Tests ===

    public function test_public_can_access_form(): void
    {
        $response = $this->get(route('ppdb.form'));
        $response->assertOk();
        $response->assertSee('PPDB');
    }

    public function test_can_submit_registration(): void
    {
        $response = $this->post(route('ppdb.store'), $this->validData());
        $response->assertRedirect(route('ppdb.success'));
        $this->assertDatabaseHas('ppdb_registrations', [
            'name' => 'AHMAD TEST',
            'status' => 'submitted',
        ]);
    }

    public function test_registration_generates_number(): void
    {
        $this->post(route('ppdb.store'), $this->validData());

        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->assertNotNull($reg);
        $this->assertStringStartsWith('PPDB-', $reg->registration_no);
    }

    public function test_validation_rejects_empty_required(): void
    {
        $response = $this->post(route('ppdb.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_validation_rejects_invalid_nik(): void
    {
        $data = $this->validData();
        $data['nik'] = '12345';
        $response = $this->post(route('ppdb.store'), $data);
        $response->assertSessionHasErrors('nik');
    }

    public function test_success_page_works(): void
    {
        $this->post(route('ppdb.store'), $this->validData());
        $response = $this->get(route('ppdb.success'));
        $response->assertOk();
    }

    // === Admin Tests ===

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('ppdb.index'));
        $response->assertOk();
    }

    public function test_guru_cannot_access_admin(): void
    {
        $this->actingAs($this->guru);
        $response = $this->get(route('ppdb.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_detail(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $response = $this->get(route('ppdb.show', $reg));
        $response->assertOk();
        $response->assertSee('AHMAD TEST');
    }

    public function test_admin_can_accept(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $response = $this->post(route('ppdb.accept', $reg));
        $response->assertRedirect();

        $reg->refresh();
        $this->assertEquals('accepted', $reg->status);
        $this->assertNotNull($reg->student_id);
        $this->assertNotNull($reg->nis_nism);
    }

    public function test_admin_can_reject(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $response = $this->post(route('ppdb.reject', $reg), [
            'rejection_reason' => 'Data tidak lengkap',
        ]);
        $response->assertRedirect();

        $reg->refresh();
        $this->assertEquals('rejected', $reg->status);
    }

    public function test_accept_creates_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $this->post(route('ppdb.accept', $reg));

        $this->assertDatabaseHas('students', [
            'name' => 'AHMAD TEST',
            'gender' => 'L',
        ]);
        $this->assertDatabaseHas('people', [
            'nik' => '6172010101010001',
        ]);
    }

    public function test_nis_generation(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        $reg->refresh();
        // NIS should be 18 digits: NSM(12) + Year(2) + Number(4)
        $this->assertEquals(18, strlen($reg->nis_nism));
        $this->assertEquals(6, strlen($reg->nis_last6));
    }

    public function test_assign_class(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        $response = $this->post(route('ppdb.assign-class', $reg), [
            'kelas' => 'I',
            'rombel' => 'A',
        ]);
        $response->assertRedirect();

        $reg->refresh();
        $this->assertEquals('I', $reg->kelas);
        $this->assertEquals('A', $reg->rombel);
    }

    public function test_admin_can_see_generate_nis_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('ppdb.generate-nis'));
        $response->assertOk();
    }
}
