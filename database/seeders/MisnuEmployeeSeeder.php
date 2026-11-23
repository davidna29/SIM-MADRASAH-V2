<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeePositionHistory;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\User;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;

class MisnuEmployeeSeeder extends Seeder
{
    /** @var array<string, string> */
    private const ROLE_BY_POSITION = [
        'KEPALA_MADRASAH' => 'kepala_madrasah',
        'WAKAMAD_KURIKULUM' => 'wakamad_kurikulum',
        'WAKAMAD_KESISWAAN' => 'wakamad_kesiswaan',
        'WAKAMAD_SARPRAS' => 'wakamad_sarpras',
        'WAKAMAD_HUMAS' => 'wakamad_humas',
        'BENDAHARA' => 'bendahara',
        'TATA_USAHA' => 'tata_usaha',
    ];

    public function run(): void
    {
        $positionId = fn (string $code) => Position::firstOrCreate(
            ['code' => $code],
            ['name' => str($code)->replace('_', ' ')->title()->toString()]
        )->id;
        $unitId = fn (string $code) => OrganizationalUnit::firstOrCreate(
            ['code' => $code],
            ['name' => str($code)->replace('_', ' ')->title()->toString()]
        )->id;

        foreach (MisnuReference::employees() as $index => $data) {
            $person = Person::where('name', $data['name'])->first()
                ?? Person::where('name', 'like', explode(',', $data['name'])[0].'%')->first();

            if (! $person) {
                $person = Person::create([
                    'nik' => sprintf('6201%012d', 800000000000 + $index),
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'religion' => 'Islam',
                    'birth_place' => $data['birth_place'],
                    'birth_date' => $data['birth_date'],
                ]);
            }

            $person->fill([
                'name' => $data['name'],
                'gender' => $data['gender'],
                'religion' => 'Islam',
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
            ])->save();

            $nip = $data['nip'];

            if ($nip && Employee::where('nip', $nip)->whereHas('person', fn ($query) => $query->whereKeyNot($person->id))->exists()) {
                $nip = null;
            }

            $employee = Employee::updateOrCreate(
                ['person_id' => $person->id],
                [
                    'nip' => $nip,
                    'nuptk' => $data['nuptk'],
                    'education_level' => $data['education_level'],
                    'major' => $data['major'],
                    'employee_status' => $data['status'],
                    'status' => 'aktif',
                    'tmt' => $data['tmt'],
                    'position_id' => $positionId($data['position']),
                    'organizational_unit_id' => $unitId($data['unit']),
                    'show_on_website' => true,
                ]
            );

            EmployeePositionHistory::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'position_id' => $employee->position_id,
                    'started_at' => $data['tmt'] ?? '2018-01-01',
                ],
                [
                    'organizational_unit_id' => $employee->organizational_unit_id,
                    'ended_at' => null,
                    'reason' => 'pengangkatan',
                ]
            );

            if ($employee->user_id && $user = User::find($employee->user_id)) {
                $role = self::ROLE_BY_POSITION[$data['position']] ?? 'guru';

                if ($user->role !== 'super_admin' && $user->role !== $role) {
                    $user->update(['role' => $role]);
                }
            }
        }
    }
}
