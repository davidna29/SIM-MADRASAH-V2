<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembiasaan_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('materi_id')->constrained('pembiasaan_materi')->cascadeOnDelete();
            $table->unsignedTinyInteger('kelas');
            $table->unsignedTinyInteger('semester');
            $table->string('tahun_pelajaran', 20)->nullable();
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'materi_id', 'kelas', 'semester']);
            $table->index(['siswa_id', 'materi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembiasaan_nilai');
    }
};
