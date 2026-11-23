<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Extracurricular;
use App\Models\User;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MisnuExtracurricularSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::with('person')->get();
        $adminId = User::where('username', 'admin')->value('id');

        foreach (MisnuReference::extracurriculars() as $data) {
            $employee = $data['pembina']
                ? $employees->first(fn (Employee $employee) => str_contains($employee->person?->name ?? '', $data['pembina']))
                : null;

            Extracurricular::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'pembina_id' => $employee?->user_id,
                    'hari' => $data['hari'],
                    'waktu' => $data['waktu'],
                    'lokasi' => $data['lokasi'],
                    'status' => 'aktif',
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
