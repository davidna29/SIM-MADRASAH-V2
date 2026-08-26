<?php

namespace Tests\Feature;

use App\Exports\PpdbExport;
use App\Models\AcademicYear;
use App\Models\Person;
use App\Models\PpdbInterest;
use App\Models\PpdbRegistration;
use App\Models\Setting;
use App\Models\User;
use App\Support\PpdbService;
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

        // Sebagian besar test registrasi/admin berasumsi PPDB sedang dibuka.
        Setting::set('ppdb_status', 'open');

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

    public function test_summary_binds_reactively_to_step(): void
    {
        // Ringkasan langkah 7 harus membaca nilai DOM saat wizard mencapai step 7,
        // bukan hanya saat init. Binding x-text wajib bergantung pada `step`
        // (via `(step + ' ') && document.querySelector(...)`) agar berisi data yang diisi,
        // dan TIDAK menggunakan koma (yang membuat Alpine menampilkan angka step).
        $response = $this->get(route('ppdb.form'));
        $response->assertOk();
        $response->assertSee("(step + ' ') && document.querySelector('[name=name]')", false);
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

    public function test_multistep_submit_not_blocked_by_hidden_required(): void
    {
        // Field wajib di langkah tersembunyi (step 4: father_name) kosong.
        // Sebelum diperbaiki, validasi native browser memblokir submit secara diam-diam.
        // Server tetap menerima POST dan mengembalikan error validasi (tidak 500/block).
        $data = $this->validData();
        unset($data['father_name']);

        $response = $this->post(route('ppdb.store'), $data);
        $response->assertRedirect(); // submission mencapai server
        $response->assertSessionHasErrors('father_name');
    }

    public function test_wizard_opens_first_step_with_error(): void
    {
        // Saat ada error validasi, wizard harus langsung membuka di langkah
        // pertama yang memuat field bermasalah (father_name -> step 4),
        // bukan selalu di step 1. Gunakan referer agar redirect-back render
        // ulang form PPDB (bukan jatuh ke /).
        $data = $this->validData();
        unset($data['father_name']);

        $response = $this->followingRedirects()
            ->post(route('ppdb.store'), $data, ['HTTP_REFERER' => url('/ppdb')]);

        $response->assertOk();
        $response->assertSee('step: 4', false);
    }

    public function test_validation_rejects_invalid_nik(): void
    {
        $data = $this->validData();
        $data['nik'] = '12345';
        $response = $this->post(route('ppdb.store'), $data);
        $response->assertSessionHasErrors('nik');
    }

    public function test_rt_rw_accepts_one_to_three_digits(): void
    {
        $data = $this->validData();
        $data['rt'] = '1';
        $data['rw'] = '12';
        $data['parent_rt'] = '3';
        $data['parent_rw'] = '123';

        $response = $this->post(route('ppdb.store'), $data);
        $response->assertRedirect(route('ppdb.success'));
    }

    public function test_rt_rw_rejects_four_digits(): void
    {
        $data = $this->validData();
        $data['rt'] = '1234';

        $response = $this->post(route('ppdb.store'), $data);
        $response->assertSessionHasErrors('rt');
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

    public function test_detail_shows_label_bukan_kode_mentah(): void
    {
        $this->actingAs($this->admin);

        // Buat registrasi dengan nilai kode pendidikan/pekerjaan
        $reg = PpdbRegistration::create(array_merge($this->validData(), [
            'registration_no' => 'PPDB-LBL-001',
            'name' => 'LABEL TEST',
            'mother_education' => '7',
            'mother_job' => '15',
            'mother_income' => 'Rp2jt – 3jt',
        ]));

        $response = $this->get(route('ppdb.show', $reg));
        $response->assertOk();
        // Harus menampilkan label, bukan kode
        $response->assertSee('D4-S1');
        $response->assertSee('Buruh (Tani/Pabrik/Bangunan)');
        $response->assertSee('Rp 2.000.000 – 3.000.000');
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
        // NIS & kelas diisi belakangan di Data Siswa (kosong saat accept)
        $this->assertNull($reg->nis_nism);
    }

    public function test_admin_pages_show_step_guide(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('ppdb.index'));
        $response->assertOk();
        $response->assertSee('Alur Pengerjaan Admin');
        $response->assertSee('Terima / Tolak');
        $response->assertSee('Export Excel');
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

    public function test_accept_rejects_duplicate_nik(): void
    {
        $this->actingAs($this->admin);

        // Dua pendaftaran dengan NIK sama
        $this->post(route('ppdb.store'), $this->validData());
        $reg1 = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $data2 = $this->validData();
        $data2['name'] = 'DUPLIKAT TEST';
        $this->post(route('ppdb.store'), $data2);
        $reg2 = PpdbRegistration::where('name', 'DUPLIKAT TEST')->first();

        // Terima yang pertama sukses
        $this->post(route('ppdb.accept', $reg1));

        // Terima yang kedua ditolak karena NIK sudah dipakai — bukan 500;
        // redirect balik ke halaman detail dan menampilkan pesan error yang ramah.
        $response = $this->followingRedirects()
            ->post(route('ppdb.accept', $reg2), [], ['HTTP_REFERER' => url('/ppdb/admin/'.$reg2->id)]);
        $response->assertOk();
        $response->assertSee('Terjadi kesalahan');
        $response->assertSee('NIK ini sudah terdaftar');

        $reg2->refresh();
        $this->assertEquals('submitted', $reg2->status);
    }

    public function test_accept_rejects_nik_already_in_people(): void
    {
        $this->actingAs($this->admin);

        // NIK sudah ada sebagai Person (siswa/pegawai/calon lain)
        Person::create([
            'nik' => '6172010101010001',
            'name' => 'ORANG LAMA',
            'gender' => 'L',
        ]);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $response = $this->post(route('ppdb.accept', $reg));
        $response->assertSessionHasErrors('nik');
        $response->assertStatus(302);

        $reg->refresh();
        $this->assertEquals('submitted', $reg->status);
    }

    public function test_export_includes_drive_links_and_follows_form_order(): void
    {
        $this->actingAs($this->admin);

        // Seed satu record lengkap via factory-like data
        $reg = PpdbRegistration::create(array_merge($this->validData(), [
            'registration_no' => 'PPDB-X-001',
            'name' => 'EXPORT TEST',
            'scanned_kk' => 'https://drive.google.com/file/d/kk-123',
            'scanned_akta' => 'https://drive.google.com/file/d/akta-123',
            'status' => 'submitted',
        ]));

        $headings = PpdbService::exportMapping();
        $keys = array_keys($headings);

        // Urutan: No. Pendaftaran -> Nama -> NIK (sesuai form langkah 1)
        $this->assertEquals('registration_no', $keys[0]);
        $this->assertEquals('name', $keys[1]);
        $this->assertEquals('nik', $keys[2]);
        // Link GDrive hadir
        $this->assertArrayHasKey('scanned_kk', $headings);
        $this->assertArrayHasKey('scanned_kk_wali', $headings);
        $this->assertArrayHasKey('scanned_akta', $headings);
        $this->assertArrayHasKey('scanned_ijazah', $headings);
        $this->assertArrayHasKey('scanned_photo', $headings);

        // Export benar-benar memetakan link GDrive ke salah satu kolom
        $export = new PpdbExport(null, null);
        $row = $export->map($reg);
        $this->assertContains('https://drive.google.com/file/d/kk-123', $row);
        $this->assertContains('https://drive.google.com/file/d/akta-123', $row);
    }

    public function test_admin_can_see_edit_page(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $response = $this->get(route('ppdb.edit', $reg));
        $response->assertOk();
        $response->assertSee('Edit Calon Siswa');
        $response->assertSee('AHMAD TEST');
    }

    public function test_admin_can_update_registration(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $data = $this->validData();
        $data['name'] = 'AHMAD DIEDIT';
        $data['hobby'] = 'Membaca';

        $response = $this->put(route('ppdb.update', $reg), $data);
        $response->assertRedirect(route('ppdb.show', $reg));

        $reg->refresh();
        $this->assertEquals('AHMAD DIEDIT', $reg->name);
        $this->assertEquals('Membaca', $reg->hobby);
        $this->assertEquals('submitted', $reg->status);
    }

    public function test_edit_available_for_accepted_status(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        $response = $this->get(route('ppdb.edit', $reg));
        $response->assertOk();
        $response->assertSee('Edit Calon Siswa');
    }

    public function test_update_rejects_invalid_data(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();

        $data = $this->validData();
        $data['nik'] = '123';

        $response = $this->put(route('ppdb.update', $reg), $data);
        $response->assertSessionHasErrors('nik');
    }

    // ── Landing page & saklar buka/tutup ────────────────────────────────

    public function test_public_sees_landing_when_ppdb_closed(): void
    {
        Setting::set('ppdb_status', 'closed');

        $response = $this->get(route('ppdb.form'));

        $response->assertOk();
        $response->assertSee('Penerimaan Peserta Didik Baru');
        $response->assertSee('Alur Pendaftaran');
        $response->assertDontSee('Nama Lengkap');
    }

    public function test_public_sees_wizard_when_ppdb_open(): void
    {
        Setting::set('ppdb_status', 'open');

        $response = $this->get(route('ppdb.form'));

        $response->assertOk();
        $response->assertSee('Pendaftaran PPDB');
        $response->assertSee('Nama Lengkap');
    }

    public function test_store_blocked_when_ppdb_closed(): void
    {
        Setting::set('ppdb_status', 'closed');

        $response = $this->post(route('ppdb.store'), $this->validData());

        $response->assertSessionHasErrors('ppdb');
        $this->assertDatabaseCount('ppdb_registrations', 0);
    }

    public function test_landing_shows_timeline_and_syarat_from_settings(): void
    {
        Setting::set('ppdb_status', 'closed');
        Setting::set('ppdb_tanggal_buka', '2026-03-02');
        Setting::set('ppdb_dokumen', "Scan Kartu Keluarga\nScan Akta Kelahiran");
        Setting::set('ppdb_kuota', '28');

        $response = $this->get(route('ppdb.form'));

        $response->assertSee('Jadwal Penting');
        $response->assertSee('Dokumen Wajib');
        $response->assertSee('Scan Kartu Keluarga');
        $response->assertSee('28 siswa');
    }

    // ── Pre-registrasi minat ────────────────────────────────────────────

    public function test_interest_store_creates_and_dedupes_by_phone(): void
    {
        Setting::set('ppdb_status', 'closed');

        $this->post(route('ppdb.interest.store'), ['name' => 'Ibu Aisyah', 'phone' => '081234567890'])
            ->assertRedirect();
        $this->post(route('ppdb.interest.store'), ['name' => 'Ibu Aisyah Baru', 'phone' => '081234567890'])
            ->assertRedirect();

        $this->assertDatabaseCount('ppdb_interests', 1);
        $this->assertDatabaseHas('ppdb_interests', ['name' => 'Ibu Aisyah Baru', 'phone' => '081234567890']);
    }

    public function test_interest_store_validates_required(): void
    {
        Setting::set('ppdb_status', 'closed');

        $this->post(route('ppdb.interest.store'), ['name' => '', 'phone' => ''])
            ->assertSessionHasErrors(['name', 'phone']);
        $this->assertDatabaseCount('ppdb_interests', 0);
    }

    // ── Pengaturan PPDB (admin) ─────────────────────────────────────────

    public function test_ppdb_roles_can_access_settings(): void
    {
        foreach (['super_admin', 'tata_usaha', 'kepala_madrasah'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('ppdb.settings'))->assertOk();
        }
    }

    public function test_guru_cannot_access_settings(): void
    {
        $this->actingAs($this->guru)->get(route('ppdb.settings'))->assertForbidden();
    }

    public function test_settings_update_persists_keys_and_faq(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('ppdb.settings.update'), [
            'ppdb_status' => 'closed',
            'ppdb_tanggal_buka' => '2026-03-02',
            'ppdb_usia_min' => '6',
            'ppdb_kuota' => '28',
            'faq_q' => ['Berapa usianya?', ''],
            'faq_a' => ['Minimal 6 tahun.', ''],
        ]);

        $response->assertRedirect();
        $this->assertEquals('closed', Setting::get('ppdb_status'));
        $this->assertEquals('2026-03-02', Setting::get('ppdb_tanggal_buka'));
        $faq = json_decode(Setting::get('ppdb_faq'), true);
        $this->assertCount(1, $faq);
        $this->assertEquals('Berapa usianya?', $faq[0]['q']);
        $this->assertEquals('Minimal 6 tahun.', $faq[0]['a']);
    }

    public function test_admin_settings_page_lists_interests(): void
    {
        Setting::set('ppdb_status', 'closed');
        PpdbInterest::create(['name' => 'Bpk Ahmad', 'phone' => '082298765432']);

        $this->actingAs($this->admin)->get(route('ppdb.settings'))
            ->assertOk()
            ->assertSee('Minat Pendaftaran')
            ->assertSee('Bpk Ahmad');
    }

    public function test_admin_can_delete_interest(): void
    {
        Setting::set('ppdb_status', 'closed');
        $interest = PpdbInterest::create(['name' => 'Bpk Ahmad', 'phone' => '082298765432']);

        $this->actingAs($this->admin)
            ->delete(route('ppdb.settings.interest.destroy', $interest))
            ->assertRedirect();

        $this->assertDatabaseCount('ppdb_interests', 0);
    }

    public function test_export_follows_status_and_search_filter(): void
    {
        Setting::set('ppdb_status', 'open');

        PpdbRegistration::create(array_merge($this->validData(), [
            'name' => 'ALIA SUBMITTED',
            'nik' => '6172010101010002',
            'registration_no' => 'PPDB-F-001',
            'status' => 'submitted',
        ]));
        PpdbRegistration::create(array_merge($this->validData(), [
            'name' => 'BUDI ACCEPTED',
            'nik' => '6172010101010003',
            'registration_no' => 'PPDB-F-002',
            'status' => 'accepted',
        ]));
        PpdbRegistration::create(array_merge($this->validData(), [
            'name' => 'ALIA REJECTED',
            'nik' => '6172010101010004',
            'registration_no' => 'PPDB-F-003',
            'status' => 'rejected',
        ]));

        // Filter status = submitted hanya memuat ALIA SUBMITTED.
        $names = (new PpdbExport('submitted', null))->query()->pluck('name');
        $this->assertEquals(['ALIA SUBMITTED'], $names->all());

        // Filter pencarian 'ALIA' memuat 2 (submitted + rejected) karena q mengecualikan draft saja.
        $names = (new PpdbExport(null, 'ALIA'))->query()->pluck('name');
        $this->assertCount(2, $names);
        $this->assertNotContains('BUDI ACCEPTED', $names->all());

        // Gabungan status + q.
        $names = (new PpdbExport('submitted', 'ALIA'))->query()->pluck('name');
        $this->assertEquals(['ALIA SUBMITTED'], $names->all());
    }
}
