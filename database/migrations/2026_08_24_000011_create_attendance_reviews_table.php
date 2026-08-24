<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tanda sudah direview per rombel+tanggal — hari yang belum direview tampil kosong di rekap
        Schema::create('attendance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('reviewed_at');
            $table->timestamps();
            $table->unique(['class_group_id', 'attendance_date'], 'attendance_review_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_reviews');
    }
};
