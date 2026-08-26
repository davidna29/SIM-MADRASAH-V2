<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no', 20)->unique();
            $table->enum('status', ['draft', 'submitted', 'accepted', 'rejected'])->default('draft');
            $table->string('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // A. Data Siswa
            $table->string('name', 100);
            $table->string('nik', 16);
            $table->string('nisn', 10)->nullable();
            $table->string('gender', 1);
            $table->string('religion', 20)->default('Islam');
            $table->string('birth_place', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('previous_school', 100)->nullable();
            $table->string('hobby', 60)->nullable();
            $table->string('ambition', 60)->nullable();
            $table->unsignedTinyInteger('child_order')->nullable();
            $table->unsignedTinyInteger('sibling_count')->nullable();
            $table->enum('ever_tk', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->enum('ever_paud', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->date('entry_date')->nullable();

            // Dokumen (link Google Drive)
            $table->string('scanned_kk', 500)->nullable();
            $table->string('scanned_kk_wali', 500)->nullable();
            $table->string('scanned_akta', 500)->nullable();
            $table->string('scanned_ijazah', 500)->nullable();
            $table->string('scanned_photo', 500)->nullable();

            // B. Kesehatan / Imunisasi
            $table->enum('imm_hepb', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->enum('imm_polio', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->enum('imm_bcg', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->enum('imm_campak', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->enum('imm_dpt', ['PERNAH', 'TIDAK'])->default('TIDAK');
            $table->enum('imm_covid', ['PERNAH', 'TIDAK'])->default('TIDAK');

            // C. Berkebutuhan Khusus
            $table->boolean('dis_deaf')->default(false);
            $table->boolean('dis_blind')->default(false);
            $table->boolean('dis_disabled')->default(false);
            $table->boolean('dis_intellectual')->default(false);
            $table->boolean('dis_behavioral')->default(false);
            $table->boolean('dis_slow_learner')->default(false);
            $table->boolean('dis_communication')->default(false);
            $table->boolean('dis_gifted')->default(false);

            // D. Alamat Siswa
            $table->string('residence_type', 60)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('province', 60)->nullable()->default('Kalimantan Tengah');
            $table->string('city', 60)->nullable()->default('Palangka Raya');
            $table->string('district', 60)->nullable();
            $table->string('village', 60)->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->string('distance', 20)->nullable();
            $table->string('transport', 60)->nullable();
            $table->string('commute_time', 30)->nullable();
            $table->string('home_phone', 20)->nullable();
            $table->string('student_phone', 20)->nullable();
            $table->string('student_email', 100)->nullable();

            // E. Data Orang Tua / Wali
            $table->string('kk_number', 16)->nullable();
            $table->string('kk_head_name', 100)->nullable();

            // Ayah
            $table->string('father_name', 100)->nullable();
            $table->string('father_status', 30)->nullable();
            $table->string('father_nik', 16)->nullable();
            $table->string('father_birth_place', 60)->nullable();
            $table->date('father_birth_date')->nullable();
            $table->string('father_education', 30)->nullable();
            $table->string('father_job', 30)->nullable();
            $table->string('father_income', 30)->nullable();
            $table->string('father_phone', 20)->nullable();

            // Ibu
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_status', 30)->nullable();
            $table->string('mother_nik', 16)->nullable();
            $table->string('mother_birth_place', 60)->nullable();
            $table->date('mother_birth_date')->nullable();
            $table->string('mother_education', 30)->nullable();
            $table->string('mother_job', 30)->nullable();
            $table->string('mother_income', 30)->nullable();
            $table->string('mother_phone', 20)->nullable();

            // Wali
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_nik', 16)->nullable();
            $table->string('guardian_birth_place', 60)->nullable();
            $table->date('guardian_birth_date')->nullable();
            $table->string('guardian_education', 30)->nullable();
            $table->string('guardian_job', 30)->nullable();
            $table->string('guardian_income', 30)->nullable();
            $table->string('guardian_phone', 20)->nullable();

            // Bantuan Sosial
            $table->string('social_kks', 30)->nullable();
            $table->string('social_pkh', 30)->nullable();
            $table->string('social_kip', 30)->nullable();

            // F. Alamat Orang Tua
            $table->string('parent_ownership', 40)->nullable();
            $table->string('parent_address', 255)->nullable();
            $table->string('parent_province', 60)->nullable()->default('Kalimantan Tengah');
            $table->string('parent_city', 60)->nullable()->default('Palangka Raya');
            $table->string('parent_district', 60)->nullable();
            $table->string('parent_village', 60)->nullable();
            $table->string('parent_rt', 3)->nullable();
            $table->string('parent_rw', 3)->nullable();
            $table->string('parent_postal_code', 5)->nullable();

            // G. Sekolah Asal
            $table->string('origin_school', 100)->nullable();
            $table->string('origin_nsm', 12)->nullable();
            $table->string('origin_npsn', 8)->nullable();
            $table->string('origin_address', 255)->nullable();

            // Admin-only (hidden from public form)
            $table->string('kelas', 10)->nullable();
            $table->string('rombel', 10)->nullable();
            $table->string('nis_nism', 18)->nullable();
            $table->string('nis_last6', 6)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_registrations');
    }
};
