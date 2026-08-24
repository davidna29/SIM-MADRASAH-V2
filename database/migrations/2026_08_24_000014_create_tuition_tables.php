<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nominal default SPP per tahun ajaran
        Schema::create('tuition_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('nominal');
            $table->timestamps();
            $table->unique(['academic_year_id']);
        });

        // Keringanan / nominal khusus per siswa per tahun ajaran
        Schema::create('tuition_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('nominal');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->unique(['student_enrollment_id', 'academic_year_id'], 'tuition_override_unique');
        });

        // Catatan pembayaran SPP per siswa per bulan
        Schema::create('tuition_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('semester', 10)->default('ganjil');
            $table->unsignedTinyInteger('bulan'); // 1..12 — bulan kalender asli
            $table->unsignedInteger('nominal');
            $table->string('status', 20)->default('belum_bayar'); // belum_bayar / lunas
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode', 30)->nullable(); // tunai / transfer
            $table->string('catatan', 255)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_enrollment_id', 'academic_year_id', 'bulan'], 'tuition_payment_unique');
            $table->index(['academic_year_id', 'student_enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_payments');
        Schema::dropIfExists('tuition_overrides');
        Schema::dropIfExists('tuition_settings');
    }
};
