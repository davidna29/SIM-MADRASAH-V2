<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->foreignId('pembina_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('hari', 10)->nullable();
            $table->time('waktu')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->string('status', 20)->default('aktif'); // aktif / nonaktif
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('extracurricular_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_bergabung')->nullable();
            $table->timestamps();
            $table->unique(['extracurricular_id', 'student_enrollment_id'], 'ekskul_member_unique');
        });

        Schema::create('extracurricular_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('status', 10); // hadir / izin / sakit / alpha
            $table->char('predikat', 1)->nullable(); // A / B / C / D — hanya saat hadir
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->unique(['extracurricular_id', 'student_enrollment_id', 'tanggal'], 'ekskul_presensi_unique');
            $table->index(['extracurricular_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extracurricular_attendances');
        Schema::dropIfExists('extracurricular_members');
        Schema::dropIfExists('extracurriculars');
    }
};
