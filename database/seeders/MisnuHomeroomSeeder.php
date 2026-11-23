<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\HomeroomAssignment;
use App\Models\User;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;

class MisnuHomeroomSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = AcademicYear::where('is_active', true)->first();

        if (! $tahun) {
            return;
        }

        $adminId = User::where('username', 'admin')->value('id');

        foreach (MisnuReference::homerooms() as $className => $employeeName) {
            $class = ClassGroup::where('name', $className)->first();
            $employee = Employee::whereHas('person', fn ($query) => $query->where('name', $employeeName))->first();

            if (! $class || ! $employee?->user_id) {
                continue;
            }

            HomeroomAssignment::updateOrCreate(
                ['class_group_id' => $class->id, 'academic_year_id' => $tahun->id],
                [
                    'user_id' => $employee->user_id,
                    'status' => 'aktif',
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
