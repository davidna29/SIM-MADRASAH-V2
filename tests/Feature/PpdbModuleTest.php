<?php

namespace Tests\Feature;

use App\Exports\PpdbExport;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\NisCounter;
use App\Models\Person;
use App\Models\PpdbRegistration;
use App\Models\Student;
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
        // NIS ditunda: baru diberikan di menu Generate NIS
        $this->assertNull($reg->nis_nism);
    }

    public function test_accepted_without_nis_appears_in_generate_page(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        $response = $this->get(route('ppdb.generate-nis'));
        $response->assertOk();
        $response->assertSee('AHMAD TEST');
        $response->assertSee('Finalisasi NIS');
    }

    public function test_commit_nis_fills_registration_and_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        $response = $this->post(route('ppdb.commit-nis'));
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $reg->refresh();
        $this->assertEquals(18, strlen($reg->nis_nism));
        // student.nis harus ikut tersinkronkan
        $this->assertNotNull($reg->student->nis);
        $this->assertEquals($reg->nis_nism, $reg->student->nis);
    }

    public function test_admin_pages_show_step_guide(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('ppdb.index'));
        $response->assertOk();
        $response->assertSee('Alur Pengerjaan Admin');
        $response->assertSee('Generate NIS');

        $response = $this->get(route('ppdb.generate-nis'));
        $response->assertSee('Alur Pengerjaan Admin');

        $response = $this->get(route('ppdb.assign-class-page'));
        $response->assertSee('Alur Pengerjaan Admin');
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

    public function test_batch_generate_nis_skips_collision(): void
    {
        $this->actingAs($this->admin);

        // Student existing dengan NIS yang akan "dibentrokkan"
        Student::create([
            'nis' => '000000000000270001',
            'name' => 'SISWA EXISTING',
            'gender' => 'L',
        ]);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        // Pastikan counter mulai dari 0 sehingga NIS pertama = ...270001 (bentrok)
        $tahun = AcademicYear::active();
        NisCounter::updateOrCreate(['academic_year_id' => $tahun->id], ['last_number' => 0]);

        $response = $this->post(route('ppdb.commit-nis'));
        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Siswa bentrok dilewati, tidak di-crash
        $reg->refresh();
        $this->assertNull($reg->nis_nism);
    }

    public function test_nis_generation(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        // NIS ditunda: belum ada setelah accept
        $reg->refresh();
        $this->assertNull($reg->nis_nism);

        // NIS di-generate via Finalisasi (Generate NIS) — 18 digit: NSM(12)+Year(2)+Number(4)
        $this->post(route('ppdb.commit-nis'));
        $reg->refresh();
        $this->assertEquals(18, strlen($reg->nis_nism));
        $this->assertEquals(6, strlen($reg->nis_last6));
    }

    public function test_assign_class(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        // Kelas harus dibuat dulu di Kelas & Penempatan
        ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $response = $this->post(route('ppdb.assign-class', $reg), [
            'class_name' => 'I-A',
        ]);
        $response->assertRedirect();

        $reg->refresh();
        $this->assertEquals('I', $reg->kelas);
        $this->assertEquals('I-A', $reg->rombel);
    }

    public function test_assign_class_rejects_nonexistent_class(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        // Kelas "X-99" tidak ada
        $response = $this->post(route('ppdb.assign-class', $reg), [
            'class_name' => 'X-99',
        ]);
        $response->assertSessionHasErrors('class_name');
    }

    public function test_assign_class_bulk(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);

        $response = $this->post(route('ppdb.assign-class-bulk'), [
            'class_name' => 'I-A',
            'registration_ids' => [$reg->id],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $reg->refresh();
        $this->assertEquals('I', $reg->kelas);
        $this->assertEquals('I-A', $reg->rombel);
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $reg->student_id,
            'class_group_id' => ClassGroup::where('name', 'I-A')->first()->id,
        ]);
    }

    public function test_assign_class_bulk_rejects_missing_class(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        // Kelas belum dibuat
        $response = $this->post(route('ppdb.assign-class-bulk'), [
            'class_name' => 'I-Z',
            'registration_ids' => [$reg->id],
        ]);
        $response->assertSessionHasErrors('class_name');
    }

    public function test_assign_class_distribute_even(): void
    {
        $this->actingAs($this->admin);

        // Dua calon diterima
        $this->post(route('ppdb.store'), $this->validData());
        $this->post(route('ppdb.store'), array_merge($this->validData(), [
            'name' => 'BUDI LAIN',
            'nik' => '6172010101010099',
        ]));
        $regs = PpdbRegistration::whereIn('name', ['AHMAD TEST', 'BUDI LAIN'])->get();
        foreach ($regs as $r) {
            $this->post(route('ppdb.accept', $r));
        }

        ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
        ClassGroup::create(['name' => 'I-B', 'grade_level' => 'I']);

        $response = $this->post(route('ppdb.assign-class-distribute'), [
            'grade_level' => 'I',
            'registration_ids' => $regs->pluck('id')->toArray(),
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Keduanya kebagian kelas (sebar rata)
        foreach ($regs as $r) {
            $r->refresh();
            $this->assertNotNull($r->kelas);
            $this->assertNotNull($r->rombel);
        }
    }

    public function test_assign_class_distribute_rejects_missing_grade(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('ppdb.store'), $this->validData());
        $reg = PpdbRegistration::where('name', 'AHMAD TEST')->first();
        $this->post(route('ppdb.accept', $reg));

        $response = $this->post(route('ppdb.assign-class-distribute'), [
            'grade_level' => 'IX',
            'registration_ids' => [$reg->id],
        ]);
        $response->assertSessionHasErrors('grade_level');
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

    public function test_admin_can_see_generate_nis_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('ppdb.generate-nis'));
        $response->assertOk();
        $response->assertSee('Acuan Nomor Urut Terakhir');
    }

    public function test_admin_can_set_nis_counter(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('ppdb.update-nis-counter'), [
            'last_number' => 25,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $tahun = AcademicYear::active();
        $this->assertEquals(25, NisCounter::where('academic_year_id', $tahun->id)->first()->last_number);
    }

    public function test_nis_counter_requires_positive_number(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('ppdb.update-nis-counter'), [
            'last_number' => -5,
        ]);
        $response->assertSessionHasErrors('last_number');
    }

    public function test_guru_cannot_set_nis_counter(): void
    {
        $this->actingAs($this->guru);

        $response = $this->post(route('ppdb.update-nis-counter'), [
            'last_number' => 25,
        ]);
        $response->assertForbidden();
    }
}
