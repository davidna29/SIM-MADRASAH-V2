<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Snapshot profil lengkap siswa dari PPDB — 1:1 dengan students, supaya data
        // pendaftar tidak hilang saat menjadi siswa aktif (anti data loss).
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_registration_id')->nullable()->constrained('ppdb_registrations')->nullOnDelete();
            $table->timestamps();

            // Identitas tambahan (A)
            $table->string('nisn', 10)->nullable();
            $table->string('previous_school', 100)->nullable();
            $table->date('entry_date')->nullable();
            $table->string('hobby', 60)->nullable();
            $table->string('ambition', 60)->nullable();
            $table->unsignedTinyInteger('child_order')->nullable();
            $table->unsignedTinyInteger('sibling_count')->nullable();
            $table->string('ever_tk', 30)->nullable(); // PERNAH / TIDAK
            $table->string('ever_paud', 30)->nullable(); // PERNAH / TIDAK

            // Alamat siswa (D)
            $table->string('address', 255)->nullable();
            $table->string('residence_type', 60)->nullable();
            $table->string('province', 60)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('district', 60)->nullable();
            $table->string('village', 60)->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->string('distance', 20)->nullable();
            $table->string('transport', 60)->nullable();
            $table->string('commute_time', 30)->nullable();
            $table->string('home_phone', 20)->nullable();

            // Keluarga & KK (E)
            $table->string('kk_number', 16)->nullable();
            $table->string('kk_head_name', 100)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('father_status', 30)->nullable();
            $table->string('father_nik', 16)->nullable();
            $table->string('father_birth_place', 60)->nullable();
            $table->date('father_birth_date')->nullable();
            $table->string('father_education', 30)->nullable();
            $table->string('father_job', 30)->nullable();
            $table->string('father_income', 30)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_status', 30)->nullable();
            $table->string('mother_nik', 16)->nullable();
            $table->string('mother_birth_place', 60)->nullable();
            $table->date('mother_birth_date')->nullable();
            $table->string('mother_education', 30)->nullable();
            $table->string('mother_job', 30)->nullable();
            $table->string('mother_income', 30)->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_nik', 16)->nullable();
            $table->string('guardian_birth_place', 60)->nullable();
            $table->date('guardian_birth_date')->nullable();
            $table->string('guardian_education', 30)->nullable();
            $table->string('guardian_job', 30)->nullable();
            $table->string('guardian_income', 30)->nullable();
            $table->string('guardian_phone', 20)->nullable();

            // Bantuan sosial
            $table->string('social_kks', 30)->nullable();
            $table->string('social_pkh', 30)->nullable();
            $table->string('social_kip', 30)->nullable();

            // Alamat orang tua (F)
            $table->string('parent_ownership', 40)->nullable();
            $table->string('parent_address', 255)->nullable();
            $table->string('parent_province', 60)->nullable();
            $table->string('parent_city', 60)->nullable();
            $table->string('parent_district', 60)->nullable();
            $table->string('parent_village', 60)->nullable();
            $table->string('parent_rt', 3)->nullable();
            $table->string('parent_rw', 3)->nullable();
            $table->string('parent_postal_code', 5)->nullable();

            // Kesehatan / imunisasi (B)
            $table->boolean('imm_hepb')->default(false);
            $table->boolean('imm_polio')->default(false);
            $table->boolean('imm_bcg')->default(false);
            $table->boolean('imm_campak')->default(false);
            $table->boolean('imm_dpt')->default(false);
            $table->boolean('imm_covid')->default(false);

            // Kebutuhan khusus (C)
            $table->boolean('dis_deaf')->default(false);
            $table->boolean('dis_blind')->default(false);
            $table->boolean('dis_disabled')->default(false);
            $table->boolean('dis_intellectual')->default(false);
            $table->boolean('dis_behavioral')->default(false);
            $table->boolean('dis_slow_learner')->default(false);
            $table->boolean('dis_communication')->default(false);
            $table->boolean('dis_gifted')->default(false);

            // Dokumen (link Google Drive) — {kk, kk_wali, akta, ijazah, photo}
            $table->json('documents')->nullable();

            $table->unique('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
