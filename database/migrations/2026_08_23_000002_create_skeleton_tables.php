<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tahun ajaran & semester — acuan seluruh transaksi akademik (PRD 7.1)
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20); // e.g. 2026/2027
            $table->string('semester', 10)->default('ganjil'); // ganjil / genap
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Kelas (tingkat + rombel)
        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20); // VII-A
            $table->string('grade_level', 10); // VII / VIII / IX
            $table->timestamps();
        });

        // Mata pelajaran
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 60);
            $table->timestamps();
        });

        // Siswa (data inti)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('name', 100);
            $table->string('gender', 1)->default('L'); // L / P
            $table->timestamps();
        });

        // Penempatan siswa per tahun ajaran — baris baru, tidak pernah menimpa (PRD 7.2)
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('aktif'); // aktif / alumni
            $table->timestamps();
            $table->unique(['academic_year_id', 'class_group_id', 'student_id'], 'enrollment_unique');
        });

        // Penugasan mengajar — guru mapel per kelas per tahun ajaran (PRD 5, 11)
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // guru
            $table->timestamps();
            $table->unique(['academic_year_id', 'class_group_id', 'subject_id'], 'assignment_unique');
        });

        // Nilai — transaksi akademik, referensi tahun ajaran & semester
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('semester', 10)->default('ganjil');
            $table->unsignedTinyInteger('score')->nullable();
            $table->timestamps();
            $table->unique(['student_enrollment_id', 'subject_id', 'semester'], 'score_unique');
        });

        // Rapor terbit — snapshot agar bisa dicetak ulang persis (PRD 7.6)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('semester', 10)->default('ganjil');
            $table->json('snapshot');
            $table->string('pdf_path', 255)->nullable();
            $table->string('status', 20)->default('draft'); // draft / terbit
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id', 'semester'], 'report_unique');
        });

        // Orang tua & relasi dengan siswa
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('guardian_student', function (Blueprint $table) {
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->primary(['guardian_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_student');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('scores');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('class_groups');
        Schema::dropIfExists('academic_years');
    }
};
