<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('jenis', 20); // akademik / nonakademik
            $table->string('nama_kegiatan', 150);
            $table->string('tingkat', 30); // sekolah/kecamatan/kabupaten/provinsi/nasional/internasional
            $table->string('penyelenggara', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('peringkat', 50)->nullable();
            $table->string('pembimbing', 100)->nullable();
            $table->string('sertifikat', 255)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('status_verifikasi', 20)->default('menunggu'); // menunggu/terverifikasi/ditolak
            $table->string('status_publikasi', 20)->default('publik'); // publik/internal
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['student_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
