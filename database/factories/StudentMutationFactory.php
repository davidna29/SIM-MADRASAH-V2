<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\Student;
use App\Models\StudentMutation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentMutation>
 */
class StudentMutationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => function () {
                $person = Person::create([
                    'nik' => fake()->unique()->numerify('################'),
                    'name' => fake()->name(),
                    'gender' => 'L',
                    'religion' => 'Islam',
                ]);

                return Student::create([
                    'person_id' => $person->id,
                    'nis' => fake()->unique()->numerify('######'),
                    'name' => $person->name,
                    'gender' => 'L',
                ])->id;
            },
            'academic_year_id' => null,
            'tanggal_mutasi' => fake()->date(),
            'sekolah_tujuan' => fake()->company().' Negeri',
            'tujuan_nsm' => fake()->numerify('#############'),
            'tujuan_npsn' => fake()->numerify('########'),
            'alasan_pindah' => fake()->randomElement(['pindah_ortu', 'pindah_alamat', 'keluarga', 'lainnya']),
            'keterangan' => fake()->sentence(),
            'no_surat' => null,
            'created_by' => null,
        ];
    }
}
