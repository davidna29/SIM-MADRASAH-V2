<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Siswa terhubung ke biodata inti di people (PRD 7.3: identitas vs akun dipisah).
        // Kolom legacy name/gender dipertahankan sementara agar view lama tidak pecah,
        // lalu diisi ulang dari people melalui seeder.
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('person_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Salin data legacy (nama/gender) ke people, lalu tautkan.
        DB::table('students')->orderBy('id')->get()->each(function ($student) {
            $personId = DB::table('people')->insertGetId([
                'nik' => $student->nis, // placeholder; diperbaiki seeder
                'name' => $student->name,
                'gender' => $student->gender,
                'religion' => 'Islam',
                'birth_place' => null,
                'birth_date' => null,
                'phone' => null,
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('students')->where('id', $student->id)->update(['person_id' => $personId]);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');
        });
    }
};
