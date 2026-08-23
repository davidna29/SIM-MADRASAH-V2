<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeePositionHistory;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use Illuminate\Database\Seeder;

class KepegawaianSeeder extends Seeder
{
    public function run(): void
    {
        $units = collect([
            ['code' => 'PIMPINAN', 'name' => 'Pimpinan'],
            ['code' => 'KURIKULUM', 'name' => 'Kurikulum'],
            ['code' => 'KESISWAAN', 'name' => 'Kesiswaan'],
            ['code' => 'SARPRAS', 'name' => 'Sarpras'],
            ['code' => 'HUMAS', 'name' => 'Humas'],
            ['code' => 'TU', 'name' => 'Tata Usaha'],
            ['code' => 'GURU', 'name' => 'Guru'],
            ['code' => 'PERPUS', 'name' => 'Perpustakaan'],
            ['code' => 'LAB', 'name' => 'Laboratorium'],
        ])->map(fn ($u) => OrganizationalUnit::updateOrCreate(['code' => $u['code']], $u));

        $positions = collect([
            ['code' => 'KEPALA_MADRASAH', 'name' => 'Kepala Madrasah'],
            ['code' => 'WAKAMAD_KURIKULUM', 'name' => 'Wakamad Kurikulum'],
            ['code' => 'WAKAMAD_KESISWAAN', 'name' => 'Wakamad Kesiswaan'],
            ['code' => 'WAKAMAD_SARPRAS', 'name' => 'Wakamad Sarpras'],
            ['code' => 'WAKAMAD_HUMAS', 'name' => 'Wakamad Humas'],
            ['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran'],
            ['code' => 'GURU_BK', 'name' => 'Guru BK'],
            ['code' => 'BENDAHARA', 'name' => 'Bendahara'],
            ['code' => 'TATA_USAHA', 'name' => 'Tata Usaha'],
            ['code' => 'PUSTAKAWAN', 'name' => 'Petugas Perpustakaan'],
            ['code' => 'LABORAN', 'name' => 'Petugas Laboratorium'],
        ])->map(fn ($p) => Position::updateOrCreate(['code' => $p['code']], $p));

        $unit = fn (string $code) => $units->firstWhere('code', $code)->id;
        $pos = fn (string $code) => $positions->firstWhere('code', $code)->id;

        $data = [
            [
                'nik' => '3508120503850001', 'name' => 'Drs. H. Ahmad Fauzi, M.Pd.', 'gender' => 'L',
                'birth_place' => 'Banyuwangi', 'birth_date' => '1985-03-12', 'phone' => '081234500001',
                'email' => 'kepala@madrasah.sch.id',
                'nip' => '198503122010011003', 'employee_status' => 'pns', 'unit' => 'PIMPINAN', 'position' => 'KEPALA_MADRASAH', 'tmt' => '2010-01-01',
            ],
            [
                'nik' => '3508131002870002', 'name' => 'Dra. Siti Nurhayati', 'gender' => 'P',
                'birth_place' => 'Jember', 'birth_date' => '1987-02-10', 'phone' => '081234500002',
                'email' => 'kurikulum@madrasah.sch.id',
                'nip' => '198702102011012004', 'employee_status' => 'pns', 'unit' => 'KURIKULUM', 'position' => 'WAKAMAD_KURIKULUM', 'tmt' => '2011-02-01',
            ],
            [
                'nik' => '3508141501900003', 'name' => 'Bapak Umar Hakim, S.Pd.', 'gender' => 'L',
                'birth_place' => 'Bondowoso', 'birth_date' => '1990-01-15', 'phone' => '081234500003',
                'email' => 'guru.umar@madrasah.sch.id',
                'nip' => '199001152019031005', 'employee_status' => 'pppk', 'unit' => 'GURU', 'position' => 'GURU_MAPEL', 'tmt' => '2019-03-01',
            ],
            [
                'nik' => '3508152107960004', 'name' => 'Ratna Dewi, S.E.', 'gender' => 'P',
                'birth_place' => 'Situbondo', 'birth_date' => '1996-07-21', 'phone' => '081234500004',
                'email' => 'bendahara@madrasah.sch.id',
                'nip' => null, 'employee_status' => 'honor', 'unit' => 'TU', 'position' => 'BENDAHARA', 'tmt' => '2020-07-01',
            ],
            [
                'nik' => '3508160504990005', 'name' => 'Sari Indah Puspitasari, A.Md.', 'gender' => 'P',
                'birth_place' => 'Probolinggo', 'birth_date' => '1999-04-05', 'phone' => '081234500005',
                'email' => 'tu@madrasah.sch.id',
                'nip' => null, 'employee_status' => 'honor', 'unit' => 'TU', 'position' => 'TATA_USAHA', 'tmt' => '2021-08-01',
            ],
            [
                'nik' => '3508171205930006', 'name' => 'Imam Syafii, S.Pd.', 'gender' => 'L',
                'birth_place' => 'Lumajang', 'birth_date' => '1993-05-12', 'phone' => '081234500006',
                'email' => 'guru.imam@madrasah.sch.id',
                'nip' => null, 'employee_status' => 'honor', 'unit' => 'GURU', 'position' => 'GURU_MAPEL', 'tmt' => '2018-07-15',
            ],
            [
                'nik' => '3508180107900007', 'name' => 'Nurul Aini, S.Pd.', 'gender' => 'P',
                'birth_place' => 'Pasuruan', 'birth_date' => '1990-07-01', 'phone' => '081234500007',
                'email' => 'guru.bk@madrasah.sch.id',
                'nip' => null, 'employee_status' => 'pppk', 'unit' => 'KESISWAAN', 'position' => 'GURU_BK', 'tmt' => '2022-09-01',
            ],
            [
                'nik' => '3508193008980008', 'name' => 'Hasan Basri, S.Kom.', 'gender' => 'L',
                'birth_place' => 'Banyuwangi', 'birth_date' => '1998-08-30', 'phone' => '081234500008',
                'email' => 'pustaka@madrasah.sch.id',
                'nip' => null, 'employee_status' => 'honor', 'unit' => 'PERPUS', 'position' => 'PUSTAKAWAN', 'tmt' => '2023-01-10',
            ],
        ];

        foreach ($data as $d) {
            $person = Person::updateOrCreate(
                ['nik' => $d['nik']],
                [
                    'name' => $d['name'], 'gender' => $d['gender'], 'religion' => 'Islam',
                    'birth_place' => $d['birth_place'], 'birth_date' => $d['birth_date'],
                    'phone' => $d['phone'], 'email' => $d['email'],
                ]
            );

            $employee = Employee::updateOrCreate(
                ['person_id' => $person->id],
                [
                    'organizational_unit_id' => $unit($d['unit']),
                    'position_id' => $pos($d['position']),
                    'nip' => $d['nip'],
                    'employee_status' => $d['employee_status'],
                    'status' => 'aktif',
                    'tmt' => $d['tmt'],
                ]
            );

            EmployeePositionHistory::firstOrCreate(
                ['employee_id' => $employee->id, 'reason' => 'pengangkatan'],
                [
                    'position_id' => $pos($d['position']),
                    'organizational_unit_id' => $unit($d['unit']),
                    'started_at' => $d['tmt'] ?? '2020-01-01',
                ]
            );
        }
    }
}
