<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kehadiran siswa — transaksi akademik, acuan tahun ajaran (PRD 7.1)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status', 10)->default('hadir'); // hadir / izin / sakit / alpha
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Satu siswa satu catatan per hari (per enrollment)
            $table->unique(['student_enrollment_id', 'attendance_date'], 'attendance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
