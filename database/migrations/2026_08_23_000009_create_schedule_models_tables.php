<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buang skema jadwal lama (relasi assignment per baris) — diganti arsitektur Model Jadwal
        Schema::dropIfExists('schedules');

        // Model Jadwal — komposisi fleksibel, beberapa bisa aktif asal tingkatan tak tumpang tindih
        Schema::create('schedule_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->time('start_time');              // jam mulai hari
            $table->unsignedTinyInteger('max_hours_per_day')->default(6);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Tingkatan yang dicakup model (I..VI) — fleksibel
        Schema::create('schedule_model_grade_levels', function (Blueprint $table) {
            $table->foreignId('schedule_model_id')->constrained()->cascadeOnDelete();
            $table->string('grade_level', 10); // I..VI
            $table->primary(['schedule_model_id', 'grade_level']);
        });

        // Slot Template — jam ke- per model (termasuk non-KBM: istirahat, upacara)
        Schema::create('schedule_model_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_model_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('period_no'); // jam ke-
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_break')->default(false);
            $table->string('label', 40)->nullable();
            $table->timestamps();
            $table->unique(['schedule_model_id', 'period_no']);
        });

        // Tabel master penyusunan — hari vertikal, kolom rombel, baris jam ke-
        Schema::create('schedule_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_model_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
            $table->string('day', 10);              // senin..sabtu
            $table->unsignedTinyInteger('period_no');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['schedule_model_id', 'class_group_id', 'day', 'period_no'], 'schedule_cell_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_cells');
        Schema::dropIfExists('schedule_model_slots');
        Schema::dropIfExists('schedule_model_grade_levels');
        Schema::dropIfExists('schedule_models');
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('day', 10);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 40)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
};
