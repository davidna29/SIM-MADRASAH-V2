<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembiasaan_materi', function (Blueprint $table) {
            $table->id();
            $table->string('modul', 10); // ppi | tahfidz
            $table->unsignedSmallInteger('no_urut');
            $table->string('nama_materi', 150);
            $table->string('jenis', 20)->nullable(); // surah | hadits (khusus tahfidz)
            $table->timestamps();
            $table->index(['modul', 'no_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembiasaan_materi');
    }
};
