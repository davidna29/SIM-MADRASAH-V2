<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counselor_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('session_date');
            $table->string('counseling_type', 50)->comment('individual, kelompok, krisis');
            $table->string('topic', 255);
            $table->text('problem_description')->nullable();
            $table->text('assessment_result')->nullable();
            $table->text('action_taken')->nullable();
            $table->text('follow_up_plan')->nullable();
            $table->string('confidentiality_level', 30)->default('plus_wali_kelas')->comment('guru_bk_only, plus_kepala, plus_wali_kelas');
            $table->string('status', 30)->default('aktif')->comment('aktif, ditutup');
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index(['counselor_user_id', 'confidentiality_level'], 'cs_counselor_conf_index');
            $table->index(['student_enrollment_id', 'status'], 'cs_enrollment_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_sessions');
    }
};
