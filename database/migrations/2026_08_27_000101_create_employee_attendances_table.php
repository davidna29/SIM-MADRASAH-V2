<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kehadiran guru & pegawai (PRD MOD-044) — berbasis kalender, tidak terikat tahun ajaran
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status', 20)->default('hadir'); // hadir / izin / sakit / dinas_luar / cuti / terlambat / pulang_awal / alpha
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Satu pegawai satu catatan per hari
            $table->unique(['employee_id', 'attendance_date'], 'employee_attendance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
