<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\MutasiInterest;
use App\Models\MutasiRegistration;
use App\Models\Setting;
use App\Models\User;
use App\Support\MutasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->admin = User::factory()->create(['role' => 'super_admin', 'username' => 'admin']);

        Setting::set('mutasi_status', 'closed');
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'RAIHAN TEST',
            'nik' => '6172010101010050',
            'gender' => 'L',
            'religion' => 'Islam',
            'origin_school' => 'MTs Negeri 1 Banjarmasin',
            'kelas_asal' => 'VIII-A',
            'kelas_tujuan' => 'VIII-A',
            'alasan_pindah' => 'Mengikuti orang tua.',
            'address' => 'Jl. Test No. 1',
            'province' => 'Kalimantan Tengah',
            'city' => 'Palangka Raya',
            'district' => 'Jekan Raya',
            'student_phone' => '085234567890',
            'father_name' => 'HENDRA TEST',
            'mother_name' => 'DEWI TEST',
            'scanned_rekomendasi' => 'https://drive.google.com/file/d/r-test',
        ], $overrides);
    }

    public function test_landing_shown_when_closed_with_new_title_and_rekomendasi_step(): void
    {
        $response = $this->get(route('pindahan.form'));

        $response->assertOk();
        $response->assertSee('Penerimaan Peserta Didik Pindahan Baru');
        $response->assertSee('Surat Rekomendasi');
    }

    public function test_form_shown_when_open(): void
    {
        Setting::set('mutasi_status', 'open');

        $response = $this->get(route('pindahan.form'));

        $response->assertOk();
        $response->assertSee('Pendaftaran Siswa Pindahan');
    }

    public function test_store_creates_registration_with_mut_number(): void
    {
        Setting::set('mutasi_status', 'open');

        $response = $this->post(route('pindahan.store'), $this->payload());

        $response->assertRedirect(route('pindahan.success'));

        $this->assertDatabaseHas('mutasi_registrations', [
            'name' => 'RAIHAN TEST',
            'status' => 'submitted',
        ]);
        $reg = MutasiRegistration::where('name', 'RAIHAN TEST')->first();
        $this->assertStringStartsWith('MUT-'.now()->year.'-', $reg->registration_no);
    }

    public function test_store_rejected_when_closed(): void
    {
        $response = $this->post(route('pindahan.store'), $this->payload());

        $response->assertSessionHasErrors('mutasi');
    }

    public function test_rekomendasi_required(): void
    {
        Setting::set('mutasi_status', 'open');

        $response = $this->post(route('pindahan.store'), $this->payload(['scanned_rekomendasi' => null]));

        $response->assertSessionHasErrors('scanned_rekomendasi');
    }

    public function test_interest_dedupe_by_phone(): void
    {
        $this->post(route('pindahan.interest.store'), ['name' => 'Bu Ani', 'phone' => '081299887766']);
        $this->post(route('pindahan.interest.store'), ['name' => 'Bu Ani Baru', 'phone' => '081299887766']);

        $this->assertSame(1, MutasiInterest::count());
    }

    public function test_admin_index_lists_registrants(): void
    {
        Setting::set('mutasi_status', 'open');
        $this->post(route('pindahan.store'), $this->payload());

        $response = $this->actingAs($this->admin)->get(route('mutasi.index'));

        $response->assertOk();
        $response->assertSee('RAIHAN TEST');
    }

    public function test_accept_copies_to_master_and_locks_edit(): void
    {
        Setting::set('mutasi_status', 'open');
        $this->post(route('pindahan.store'), $this->payload());
        $registration = MutasiRegistration::first();

        $student = MutasiService::accept($registration);
        $student->refresh();

        // Master terisi
        $this->assertDatabaseHas('students', ['id' => $student->id, 'origin_school' => 'MTs Negeri 1 Banjarmasin']);
        $this->assertSame('HENDRA TEST', $student->guardianByRelation('ayah')?->name);
        $this->assertSame('accepted', $registration->fresh()->status);

        // Edit terkunci
        $this->actingAs($this->admin)->get(route('mutasi.edit', $registration))->assertForbidden();
        $this->actingAs($this->admin)->put(route('mutasi.update', $registration), $this->payload())->assertForbidden();
    }

    public function test_accept_modal_notice_shown(): void
    {
        Setting::set('mutasi_status', 'open');
        $this->post(route('pindahan.store'), $this->payload());
        $registration = MutasiRegistration::first();

        $response = $this->actingAs($this->admin)->get(route('mutasi.show', $registration));

        $response->assertOk();
        $response->assertSee('MENGUNCI seluruh pengeditan di modul mutasi');
    }

    public function test_reject_works(): void
    {
        Setting::set('mutasi_status', 'open');
        $this->post(route('pindahan.store'), $this->payload());
        $registration = MutasiRegistration::first();

        $this->actingAs($this->admin)->post(route('mutasi.reject', $registration), [
            'rejection_reason' => 'Berkas tidak lengkap',
        ]);

        $this->assertSame('rejected', $registration->fresh()->status);
        $this->assertSame('Berkas tidak lengkap', $registration->fresh()->rejection_reason);
    }

    public function test_settings_update_status_and_faq(): void
    {
        $this->actingAs($this->admin)->put(route('mutasi.settings.update'), [
            'mutasi_status' => 'open',
            'mutasi_syarat' => "Surat Rekomendasi Madrasah\nRapor",
            'faq_q' => ['Apa syaratnya?'],
            'faq_a' => ['Sertakan surat rekomendasi madrasah.'],
        ]);

        $this->assertSame('open', Setting::get('mutasi_status'));
        $this->assertStringContainsString('Rekomendasi', (string) Setting::get('mutasi_syarat'));
        $faq = json_decode((string) Setting::get('mutasi_faq'), true);
        $this->assertSame('Apa syaratnya?', $faq[0]['q'] ?? null);
    }

    public function test_guru_cannot_access_admin_mutasi(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('mutasi.index'))->assertForbidden();
    }
}
