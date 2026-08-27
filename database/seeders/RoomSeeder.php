<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::with('person')->where('status', 'aktif')->get();
        $pj = fn (string $name) => $employees->first(fn ($e) => str_contains($e->person->name, $name))?->id;

        $rooms = [
            ['code' => 'R-001', 'name' => 'Ruang Guru', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 30, 'condition' => 'baik', 'employee_id' => $pj('ERNA')],
            ['code' => 'R-002', 'name' => 'Kantor Kepala Madrasah', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 5, 'condition' => 'baik', 'employee_id' => $pj('ERNA')],
            ['code' => 'R-003', 'name' => 'Kantor Tata Usaha', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 8, 'condition' => 'baik', 'employee_id' => $pj('ZAHRATUNNISA')],
            ['code' => 'R-004', 'name' => 'Ruang BK', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 4, 'condition' => 'baik', 'employee_id' => null],
            ['code' => 'R-005', 'name' => 'Ruang Perpustakaan', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 20, 'condition' => 'baik', 'employee_id' => null],
            ['code' => 'R-006', 'name' => 'Ruang Kelas I', 'type' => 'ruangan', 'building' => 'Gedung A', 'floor' => 'Lantai 1', 'capacity' => 36, 'condition' => 'baik', 'employee_id' => null],
            ['code' => 'R-007', 'name' => 'Ruang Kelas II', 'type' => 'ruangan', 'building' => 'Gedung A', 'floor' => 'Lantai 1', 'capacity' => 36, 'condition' => 'baik', 'employee_id' => null],
            ['code' => 'R-008', 'name' => 'Aula', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 2', 'capacity' => 200, 'condition' => 'rusak_ringan', 'employee_id' => $pj('MUHAMMAD ARSYAD')],
            ['code' => 'R-009', 'name' => 'Lab IPA', 'type' => 'laboratorium', 'building' => 'Gedung B', 'floor' => 'Lantai 1', 'capacity' => 30, 'condition' => 'baik', 'employee_id' => $pj('ANWARI')],
            ['code' => 'R-010', 'name' => 'Lab Komputer', 'type' => 'laboratorium', 'building' => 'Gedung B', 'floor' => 'Lantai 2', 'capacity' => 25, 'condition' => 'baik', 'employee_id' => $pj('M. DEDE')],
            ['code' => 'R-011', 'name' => 'Lab Bahasa', 'type' => 'laboratorium', 'building' => 'Gedung B', 'floor' => 'Lantai 1', 'capacity' => 30, 'condition' => 'dalam_perbaikan', 'employee_id' => null],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['code' => $room['code']], $room);
        }
    }
}
