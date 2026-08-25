<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\HomeroomAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeroomModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $guru;

    protected ClassGroup $kelas;

    protected User $wakad;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->wakad = User::factory()->create(['role' => 'wakamad_kurikulum']);
        $this->guru = User::factory()->create(['role' => 'guru', 'name' => 'Guru Wali']);

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $this->kelas = ClassGroup::create(['name' => 'I-A', 'grade_level' => 'I']);
    }

    public function test_admin_can_assign_wali_kelas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $this->guru->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('homeroom_assignments', [
            'class_group_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'status' => 'aktif',
        ]);
    }

    public function test_wakad_can_assign_wali_kelas(): void
    {
        $response = $this->actingAs($this->wakad)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $this->guru->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('homeroom_assignments', [
            'class_group_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
        ]);
    }

    public function test_replacing_wali_kelas_marks_previous_as_selesai(): void
    {
        $guru2 = User::factory()->create(['role' => 'guru']);

        $this->actingAs($this->admin)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $this->guru->id,
        ]);

        $this->actingAs($this->admin)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $guru2->id,
        ]);

        $this->assertDatabaseHas('homeroom_assignments', [
            'class_group_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('homeroom_assignments', [
            'class_group_id' => $this->kelas->id,
            'user_id' => $guru2->id,
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_remove_wali_kelas(): void
    {
        $homeroom = HomeroomAssignment::create([
            'class_group_id' => $this->kelas->id,
            'academic_year_id' => AcademicYear::active()->id,
            'user_id' => $this->guru->id,
            'status' => 'aktif',
        ]);

        $this->actingAs($this->admin)->delete(route('kelas.wali.destroy', [$this->kelas, $homeroom]));

        $this->assertDatabaseHas('homeroom_assignments', [
            'id' => $homeroom->id,
            'status' => 'selesai',
        ]);
    }

    public function test_guru_cannot_assign_wali_kelas(): void
    {
        $guruBiasa = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guruBiasa)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $this->guru->id,
        ]);

        $response->assertForbidden();
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $response = $this->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $this->guru->id,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_invalid_user_id_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => 99999,
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    public function test_one_wali_kelas_per_class_per_year(): void
    {
        $guru2 = User::factory()->create(['role' => 'guru']);

        $this->actingAs($this->admin)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $this->guru->id,
        ]);

        $this->actingAs($this->admin)->post(route('kelas.wali.store', $this->kelas), [
            'user_id' => $guru2->id,
        ]);

        $this->assertDatabaseCount('homeroom_assignments', 2);
        $this->assertDatabaseHas('homeroom_assignments', [
            'class_group_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'status' => 'selesai',
        ]);
        $this->assertDatabaseHas('homeroom_assignments', [
            'class_group_id' => $this->kelas->id,
            'user_id' => $guru2->id,
            'status' => 'aktif',
        ]);
    }
}
