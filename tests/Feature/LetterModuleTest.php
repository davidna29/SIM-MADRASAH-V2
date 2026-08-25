<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LetterModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $tataUsaha;

    protected User $guru;

    protected AcademicYear $tahun;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahun = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->tataUsaha = User::factory()->create(['role' => 'tata_usaha']);
        $this->guru = User::factory()->create(['role' => 'guru']);

        // Create categories
        LetterCategory::create(['name' => 'Undangan', 'sort_order' => 1]);
        LetterCategory::create(['name' => 'Pemberitahuan', 'sort_order' => 2]);
    }

    public function test_can_list_letters(): void
    {
        $this->actingAs($this->admin);

        $letter = Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Kantor Kemenag',
            'subject' => 'Edaran Ujian',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $response = $this->get(route('surat.index', ['type' => 'masuk']));

        $response->assertOk();
        $response->assertSee('Edaran Ujian');
    }

    public function test_can_create_letter(): void
    {
        $this->actingAs($this->tataUsaha);

        $response = $this->post(route('surat.store'), [
            'type' => 'masuk',
            'date' => now()->format('Y-m-d'),
            'from_to' => 'Dinas Pendidikan',
            'subject' => 'Surat Undangan Rapat',
            'description' => 'Undangan rapat koordinasi',
            'status' => 'diterima',
            'priority' => 'biasa',
            'category' => 'Undangan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'subject' => 'Surat Undangan Rapat',
            'type' => 'masuk',
        ]);
    }

    public function test_can_update_letter(): void
    {
        $this->actingAs($this->tataUsaha);

        $letter = Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Kantor Kemenag',
            'subject' => 'Edaran Ujian',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->tataUsaha->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $response = $this->put(route('surat.update', $letter), [
            'date' => now()->format('Y-m-d'),
            'from_to' => 'Kantor Kemenag',
            'subject' => 'Edaran Ujian - Updated',
            'status' => 'diproses',
            'priority' => 'penting',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'subject' => 'Edaran Ujian - Updated',
            'status' => 'diproses',
        ]);
    }

    public function test_can_delete_letter(): void
    {
        $this->actingAs($this->admin);

        $letter = Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Kantor Kemenag',
            'subject' => 'Edaran Ujian',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $response = $this->delete(route('surat.destroy', $letter));

        $response->assertRedirect();
        $this->assertDatabaseMissing('letters', ['id' => $letter->id]);
    }

    public function test_can_filter_by_type(): void
    {
        $this->actingAs($this->admin);

        Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Pengirim A',
            'subject' => 'Surat Masuk A',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        Letter::create([
            'type' => 'keluar',
            'date' => now(),
            'from_to' => 'Penerima B',
            'subject' => 'Surat Keluar B',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $response = $this->get(route('surat.index', ['type' => 'masuk']));

        $response->assertOk();
        $response->assertSee('Surat Masuk A');
        $response->assertDontSee('Surat Keluar B');
    }

    public function test_can_filter_by_status(): void
    {
        $this->actingAs($this->admin);

        Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Pengirim A',
            'subject' => 'Surat Diterima',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Pengirim B',
            'subject' => 'Surat Selesai',
            'status' => 'selesai',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $response = $this->get(route('surat.index', ['type' => 'masuk', 'status' => 'diterima']));

        $response->assertOk();
        $response->assertSee('Surat Diterima');
        $response->assertDontSee('Surat Selesai');
    }

    public function test_can_dispose_letter(): void
    {
        $this->actingAs($this->admin);

        $letter = Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Kantor Kemenag',
            'subject' => 'Edaran Ujian',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->admin->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $response = $this->patch(route('surat.disposition', $letter), [
            'disposition_to' => 'Wakamad Kurikulum',
            'disposition_note' => 'Mohon ditindaklanjuti',
            'status' => 'diproses',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'disposition_to' => 'Wakamad Kurikulum',
            'status' => 'diproses',
        ]);
    }

    public function test_cannot_access_unauthorized(): void
    {
        $this->actingAs($this->guru);

        $response = $this->get(route('surat.index', ['type' => 'masuk']));

        $response->assertForbidden();
    }

    public function test_tata_usaha_can_access(): void
    {
        $this->actingAs($this->tataUsaha);

        $response = $this->get(route('surat.index', ['type' => 'masuk']));

        $response->assertOk();
    }

    public function test_guru_cannot_dispose(): void
    {
        $this->actingAs($this->tataUsaha);

        $letter = Letter::create([
            'type' => 'masuk',
            'date' => now(),
            'from_to' => 'Kantor Kemenag',
            'subject' => 'Edaran Ujian',
            'status' => 'diterima',
            'priority' => 'biasa',
            'recorded_by' => $this->tataUsaha->id,
            'academic_year_id' => $this->tahun->id,
        ]);

        $this->actingAs($this->guru);

        $response = $this->patch(route('surat.disposition', $letter), [
            'disposition_to' => 'Wakamad Kurikulum',
            'status' => 'diproses',
        ]);

        $response->assertForbidden();
    }
}
