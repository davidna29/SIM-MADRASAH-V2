<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('jenis', 20)->default('agenda'); // agenda / pengumuman
            $table->date('tanggal')->nullable();
            $table->time('waktu')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->string('penanggung_jawab', 100)->nullable();
            $table->text('isi')->nullable();
            $table->string('target', 20)->default('publik'); // publik / internal
            $table->date('tampil_mulai');
            $table->date('tampil_selesai')->nullable();
            $table->string('status', 20)->default('aktif'); // aktif / arsip
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'tampil_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};
