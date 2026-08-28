<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pindahkan snapshot student_profiles (tabel side-car) ke master:
     * students / people / guardians + pivot relation, lalu drop tabelnya.
     */
    public function up(): void
    {
        if (! Schema::hasTable('student_profiles')) {
            return;
        }

        $profiles = DB::table('student_profiles')->get();

        foreach ($profiles as $p) {
            $student = DB::table('students')->find($p->student_id);
            if (! $student) {
                continue;
            }

            // 1. Person (alamat & hp)
            if ($student->person_id) {
                DB::table('people')->where('id', $student->person_id)->update([
                    'address' => $p->address,
                    'province' => $p->province,
                    'city' => $p->city,
                    'district' => $p->district,
                    'village' => $p->village,
                    'rt' => $p->rt,
                    'rw' => $p->rw,
                    'postal_code' => $p->postal_code,
                    'home_phone' => $p->home_phone,
                ]);
            }

            // 2. Student (profil akademik)
            DB::table('students')->where('id', $student->id)->update([
                'nisn' => $p->nisn,
                'previous_school' => $p->previous_school,
                'origin_school' => $p->origin_school,
                'origin_nsm' => $p->origin_nsm,
                'origin_npsn' => $p->origin_npsn,
                'origin_address' => $p->origin_address,
                'entry_date' => $p->entry_date,
                'hobby' => $p->hobby,
                'ambition' => $p->ambition,
                'child_order' => $p->child_order,
                'sibling_count' => $p->sibling_count,
                'ever_tk' => $p->ever_tk,
                'ever_paud' => $p->ever_paud,
                'residence_type' => $p->residence_type,
                'distance' => $p->distance,
                'transport' => $p->transport,
                'commute_time' => $p->commute_time,
                'kk_number' => $p->kk_number,
                'kk_head_name' => $p->kk_head_name,
                'social_kks' => $p->social_kks,
                'social_pkh' => $p->social_pkh,
                'social_kip' => $p->social_kip,
                'parent_ownership' => $p->parent_ownership,
                'parent_address' => $p->parent_address,
                'parent_province' => $p->parent_province,
                'parent_city' => $p->parent_city,
                'parent_district' => $p->parent_district,
                'parent_village' => $p->parent_village,
                'parent_rt' => $p->parent_rt,
                'parent_rw' => $p->parent_rw,
                'parent_postal_code' => $p->parent_postal_code,
                'imm_hepb' => $p->imm_hepb,
                'imm_polio' => $p->imm_polio,
                'imm_bcg' => $p->imm_bcg,
                'imm_campak' => $p->imm_campak,
                'imm_dpt' => $p->imm_dpt,
                'imm_covid' => $p->imm_covid,
                'dis_deaf' => $p->dis_deaf,
                'dis_blind' => $p->dis_blind,
                'dis_disabled' => $p->dis_disabled,
                'dis_intellectual' => $p->dis_intellectual,
                'dis_behavioral' => $p->dis_behavioral,
                'dis_slow_learner' => $p->dis_slow_learner,
                'dis_communication' => $p->dis_communication,
                'dis_gifted' => $p->dis_gifted,
                'documents' => $p->documents,
            ]);

            // 3. Guardians (dari snapshot keluarga) + pivot relation
            $insertGuardian = function (array $data, ?string $relation, int $studentId) {
                if (empty($data['name'])) {
                    return;
                }
                $guardianId = null;
                if (! empty($data['nik'])) {
                    $guardianId = DB::table('guardians')->where('nik', $data['nik'])->value('id');
                }
                $guardianId = $guardianId ?: DB::table('guardians')->insertGetId([
                    'user_id' => null,
                    'name' => $data['name'],
                    'nik' => $data['nik'] ?? null,
                    'status' => $data['status'] ?? null,
                    'birth_place' => $data['birth_place'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'education' => $data['education'] ?? null,
                    'job' => $data['job'] ?? null,
                    'income' => $data['income'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $exists = DB::table('guardian_student')
                    ->where('guardian_id', $guardianId)
                    ->where('student_id', $studentId)
                    ->exists();
                if (! $exists) {
                    DB::table('guardian_student')->insert([
                        'guardian_id' => $guardianId,
                        'student_id' => $studentId,
                        'relation' => $relation,
                    ]);
                }
            };

            $insertGuardian([
                'name' => $p->father_name,
                'nik' => $p->father_nik,
                'status' => $p->father_status,
                'birth_place' => $p->father_birth_place,
                'birth_date' => $p->father_birth_date,
                'education' => $p->father_education,
                'job' => $p->father_job,
                'income' => $p->father_income,
                'phone' => $p->father_phone,
            ], 'ayah', $student->id);

            $insertGuardian([
                'name' => $p->mother_name,
                'nik' => $p->mother_nik,
                'status' => $p->mother_status,
                'birth_place' => $p->mother_birth_place,
                'birth_date' => $p->mother_birth_date,
                'education' => $p->mother_education,
                'job' => $p->mother_job,
                'income' => $p->mother_income,
                'phone' => $p->mother_phone,
            ], 'ibu', $student->id);

            $insertGuardian([
                'name' => $p->guardian_name,
                'nik' => $p->guardian_nik,
                'birth_place' => $p->guardian_birth_place,
                'birth_date' => $p->guardian_birth_date,
                'education' => $p->guardian_education,
                'job' => $p->guardian_job,
                'income' => $p->guardian_income,
                'phone' => $p->guardian_phone,
            ], 'wali', $student->id);
        }

        Schema::dropIfExists('student_profiles');
    }

    public function down(): void
    {
        // Tidak dikembalikan (profil side-car sudah tidak dipakai).
    }
};
