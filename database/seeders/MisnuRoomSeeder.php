<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Room;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;

class MisnuRoomSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::with('person')->get();
        $pj = fn (?string $name) => $name
            ? $employees->first(fn (Employee $employee) => str_contains($employee->person?->name ?? '', $name))?->id
            : null;

        foreach (MisnuReference::rooms() as $room) {
            Room::updateOrCreate(
                ['code' => $room['code']],
                [
                    'name' => $room['name'],
                    'type' => $room['type'],
                    'building' => $room['building'],
                    'floor' => $room['floor'],
                    'capacity' => $room['capacity'],
                    'condition' => $room['condition'],
                    'employee_id' => $pj($room['employee']),
                    'description' => $room['description'],
                ]
            );
        }
    }
}
