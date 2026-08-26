<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembiasaan_materi_periode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('pembiasaan_materi')->cascadeOnDelete();
            $table->unsignedTinyInteger('kelas'); // 1-6
            $table->unsignedTinyInteger('semester'); // 1 | 2
            $table->boolean('aktif')->default(false);
            $table->timestamps();
            $table->unique(['materi_id', 'kelas', 'semester']);
            $table->index(['materi_id', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembiasaan_materi_periode');
    }
};
