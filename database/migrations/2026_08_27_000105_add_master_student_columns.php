<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pelebaran master agar benar-benar menampung data pendaftaran PPDB asli
        // (tanpa tabel side-car): people = identitas+alamat; students = profil akademik;
        // guardians = orang tua/wali; guardian_student = relasi hubungan.

        Schema::table('people', function (Blueprint $table) {
            $table->string('address', 255)->nullable()->after('email');
            $table->string('province', 60)->nullable()->after('address');
            $table->string('city', 60)->nullable()->after('province');
            $table->string('district', 60)->nullable()->after('city');
            $table->string('village', 60)->nullable()->after('district');
            $table->string('rt', 3)->nullable()->after('village');
            $table->string('rw', 3)->nullable()->after('rt');
            $table->string('postal_code', 5)->nullable()->after('rw');
            $table->string('home_phone', 20)->nullable()->after('postal_code');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('nisn', 10)->nullable()->after('nis');
            $table->string('previous_school', 100)->nullable()->after('nisn');
            $table->string('origin_school', 100)->nullable()->after('previous_school');
            $table->string('origin_nsm', 12)->nullable()->after('origin_school');
            $table->string('origin_npsn', 8)->nullable()->after('origin_nsm');
            $table->string('origin_address', 255)->nullable()->after('origin_npsn');
            $table->date('entry_date')->nullable()->after('origin_address');
            $table->string('hobby', 60)->nullable()->after('entry_date');
            $table->string('ambition', 60)->nullable()->after('hobby');
            $table->unsignedTinyInteger('child_order')->nullable()->after('ambition');
            $table->unsignedTinyInteger('sibling_count')->nullable()->after('child_order');
            $table->string('ever_tk', 30)->nullable()->after('sibling_count');
            $table->string('ever_paud', 30)->nullable()->after('ever_tk');
            $table->string('residence_type', 60)->nullable()->after('ever_paud');
            $table->string('distance', 20)->nullable()->after('residence_type');
            $table->string('transport', 60)->nullable()->after('distance');
            $table->string('commute_time', 30)->nullable()->after('transport');
            $table->string('kk_number', 16)->nullable()->after('commute_time');
            $table->string('kk_head_name', 100)->nullable()->after('kk_number');
            $table->string('social_kks', 30)->nullable()->after('kk_head_name');
            $table->string('social_pkh', 30)->nullable()->after('social_kks');
            $table->string('social_kip', 30)->nullable()->after('social_pkh');
            $table->string('parent_ownership', 40)->nullable()->after('social_kip');
            $table->string('parent_address', 255)->nullable()->after('parent_ownership');
            $table->string('parent_province', 60)->nullable()->after('parent_address');
            $table->string('parent_city', 60)->nullable()->after('parent_province');
            $table->string('parent_district', 60)->nullable()->after('parent_city');
            $table->string('parent_village', 60)->nullable()->after('parent_district');
            $table->string('parent_rt', 3)->nullable()->after('parent_village');
            $table->string('parent_rw', 3)->nullable()->after('parent_rt');
            $table->string('parent_postal_code', 5)->nullable()->after('parent_rw');
            $table->boolean('imm_hepb')->default(false);
            $table->boolean('imm_polio')->default(false);
            $table->boolean('imm_bcg')->default(false);
            $table->boolean('imm_campak')->default(false);
            $table->boolean('imm_dpt')->default(false);
            $table->boolean('imm_covid')->default(false);
            $table->boolean('dis_deaf')->default(false);
            $table->boolean('dis_blind')->default(false);
            $table->boolean('dis_disabled')->default(false);
            $table->boolean('dis_intellectual')->default(false);
            $table->boolean('dis_behavioral')->default(false);
            $table->boolean('dis_slow_learner')->default(false);
            $table->boolean('dis_communication')->default(false);
            $table->boolean('dis_gifted')->default(false);
            $table->json('documents')->nullable();
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->after('name');
            $table->index('nik');
            $table->string('status', 30)->nullable()->after('nik');
            $table->string('birth_place', 60)->nullable()->after('status');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('education', 30)->nullable()->after('birth_date');
            $table->string('job', 30)->nullable()->after('education');
            $table->string('income', 30)->nullable()->after('job');
            $table->string('phone', 20)->nullable()->after('income');
        });

        Schema::table('guardian_student', function (Blueprint $table) {
            $table->string('relation', 20)->nullable()->after('student_id'); // ayah / ibu / wali
        });
    }

    public function down(): void
    {
        Schema::table('guardian_student', function (Blueprint $table) {
            $table->dropColumn('relation');
        });
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->dropColumn(['nik', 'status', 'birth_place', 'birth_date', 'education', 'job', 'income', 'phone']);
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'nisn', 'previous_school', 'origin_school', 'origin_nsm', 'origin_npsn', 'origin_address',
                'entry_date', 'hobby', 'ambition', 'child_order', 'sibling_count',
                'ever_tk', 'ever_paud', 'residence_type', 'distance', 'transport', 'commute_time',
                'kk_number', 'kk_head_name', 'social_kks', 'social_pkh', 'social_kip',
                'parent_ownership', 'parent_address', 'parent_province', 'parent_city', 'parent_district',
                'parent_village', 'parent_rt', 'parent_rw', 'parent_postal_code',
                'imm_hepb', 'imm_polio', 'imm_bcg', 'imm_campak', 'imm_dpt', 'imm_covid',
                'dis_deaf', 'dis_blind', 'dis_disabled', 'dis_intellectual', 'dis_behavioral',
                'dis_slow_learner', 'dis_communication', 'dis_gifted', 'documents',
            ]);
        });
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['address', 'province', 'city', 'district', 'village', 'rt', 'rw', 'postal_code', 'home_phone']);
        });
    }
};
