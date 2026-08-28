<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lengkapi kolom mutasi_registrasi agar selengkap ppdb_registrations
        Schema::table('mutasi_registrations', function (Blueprint $table) {
            // Identitas tambahan
            $table->string('previous_school', 100)->nullable()->after('birth_date');
            $table->string('hobby', 60)->nullable()->after('previous_school');
            $table->string('ambition', 60)->nullable()->after('hobby');
            $table->unsignedTinyInteger('child_order')->nullable()->after('ambition');
            $table->unsignedTinyInteger('sibling_count')->nullable()->after('child_order');
            $table->string('ever_tk', 30)->nullable()->after('sibling_count');
            $table->string('ever_paud', 30)->nullable()->after('ever_tk');
            $table->date('entry_date')->nullable()->after('ever_paud');

            // Kesehatan / imunisasi
            $table->boolean('imm_hepb')->default(false);
            $table->boolean('imm_polio')->default(false);
            $table->boolean('imm_bcg')->default(false);
            $table->boolean('imm_campak')->default(false);
            $table->boolean('imm_dpt')->default(false);
            $table->boolean('imm_covid')->default(false);

            // Kebutuhan khusus
            $table->boolean('dis_deaf')->default(false);
            $table->boolean('dis_blind')->default(false);
            $table->boolean('dis_disabled')->default(false);
            $table->boolean('dis_intellectual')->default(false);
            $table->boolean('dis_behavioral')->default(false);
            $table->boolean('dis_slow_learner')->default(false);
            $table->boolean('dis_communication')->default(false);
            $table->boolean('dis_gifted')->default(false);

            // Alamat lanjutan
            $table->string('residence_type', 60)->nullable();
            $table->string('distance', 20)->nullable();
            $table->string('transport', 60)->nullable();
            $table->string('commute_time', 30)->nullable();
            $table->string('home_phone', 20)->nullable();

            // Keluarga / KK
            $table->string('kk_number', 16)->nullable();
            $table->string('kk_head_name', 100)->nullable();
            $table->string('father_status', 30)->nullable();
            $table->string('father_birth_place', 60)->nullable();
            $table->date('father_birth_date')->nullable();
            $table->string('father_education', 30)->nullable();
            $table->string('father_income', 30)->nullable();
            $table->string('mother_status', 30)->nullable();
            $table->string('mother_birth_place', 60)->nullable();
            $table->date('mother_birth_date')->nullable();
            $table->string('mother_education', 30)->nullable();
            $table->string('mother_income', 30)->nullable();
            $table->date('guardian_birth_date')->nullable();
            $table->string('guardian_education', 30)->nullable();
            $table->string('guardian_job', 30)->nullable();
            $table->string('guardian_income', 30)->nullable();

            // Bantuan sosial
            $table->string('social_kks', 30)->nullable();
            $table->string('social_pkh', 30)->nullable();
            $table->string('social_kip', 30)->nullable();

            // Alamat orang tua
            $table->string('parent_ownership', 40)->nullable();
            $table->string('parent_address', 255)->nullable();
            $table->string('parent_province', 60)->nullable();
            $table->string('parent_city', 60)->nullable();
            $table->string('parent_district', 60)->nullable();
            $table->string('parent_village', 60)->nullable();
            $table->string('parent_rt', 3)->nullable();
            $table->string('parent_rw', 3)->nullable();
            $table->string('parent_postal_code', 5)->nullable();

            // Dokumen tambahan
            $table->string('scanned_kk_wali', 500)->nullable();
            $table->string('scanned_ijazah', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mutasi_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'previous_school', 'hobby', 'ambition', 'child_order', 'sibling_count',
                'ever_tk', 'ever_paud', 'entry_date',
                'imm_hepb', 'imm_polio', 'imm_bcg', 'imm_campak', 'imm_dpt', 'imm_covid',
                'dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual',
                'dis_behavioral', 'dis_slow_learner', 'dis_communication', 'dis_gifted',
                'residence_type', 'distance', 'transport', 'commute_time', 'home_phone',
                'kk_number', 'kk_head_name',
                'father_status', 'father_birth_place', 'father_birth_date', 'father_education', 'father_income',
                'mother_status', 'mother_birth_place', 'mother_birth_date', 'mother_education', 'mother_income',
                'guardian_birth_date', 'guardian_education', 'guardian_job', 'guardian_income',
                'social_kks', 'social_pkh', 'social_kip',
                'parent_ownership', 'parent_address', 'parent_province', 'parent_city',
                'parent_district', 'parent_village', 'parent_rt', 'parent_rw', 'parent_postal_code',
                'scanned_kk_wali', 'scanned_ijazah',
            ]);
        });
    }
};
