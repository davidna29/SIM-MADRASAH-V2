<?php

namespace Database\Seeders;

use App\Models\ClassGroup;
use App\Support\Misnu\MisnuReference;
use Illuminate\Database\Seeder;

class MisnuClassGroupSeeder extends Seeder
{
    public function run(): void
    {
        foreach (MisnuReference::classGroups() as $class) {
            ClassGroup::updateOrCreate(
                ['name' => $class['name']],
                ['grade_level' => $class['grade_level']]
            );
        }
    }
}
