<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicTeacherPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected Position $position;

    protected OrganizationalUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->position = Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        $this->unit = OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
    }

    protected function employee(string $name, string $nik, bool $withPhoto, bool $show = true): Employee
    {
        $person = Person::create(['nik' => $nik, 'name' => $name, 'gender' => 'L', 'religion' => 'Islam']);

        return Employee::create([
            'person_id' => $person->id,
            'position_id' => $this->position->id,
            'organizational_unit_id' => $this->unit->id,
            'employee_status' => 'honor',
            'status' => 'aktif',
            'show_on_website' => $show,
            'photo_path' => $withPhoto ? 'pegawai/'.$person->id.'.jpg' : null,
        ]);
    }

    public function test_guru_and_struktur_show_teacher_photos_and_fallback(): void
    {
        Storage::fake('public');
        $withPhoto = $this->employee('GURU FOTO', '3508990000000001', true);
        $this->employee('GURU TANPA FOTO', '3508990000000002', false);
        $this->employee('GURU TERSEMBUNYI', '3508990000000003', true, false);

        Storage::disk('public')->put($withPhoto->photo_path, 'foto');

        $this->get(route('publik.guru'))
            ->assertOk()
            ->assertSee($withPhoto->photoUrl())
            ->assertSee('GURU FOTO')
            ->assertSee('GURU TANPA FOTO')
            ->assertDontSee('GURU TERSEMBUNYI');

        $this->get(route('publik.struktur'))
            ->assertOk()
            ->assertSee($withPhoto->photoUrl())
            ->assertSee('GURU FOTO')
            ->assertDontSee('GURU TERSEMBUNYI');
    }
}
