<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('kategori', 50);
            $table->string('tingkat', 20); // ringan/sedang/berat
            $table->unsignedTinyInteger('poin')->default(0);
            $table->date('tanggal_kejadian');
            $table->text('kronologi');
            $table->string('pelapor', 100)->nullable();
            $table->string('bukti', 255)->nullable();
            $table->string('tindakan', 255)->nullable();
            $table->boolean('pemanggilan_ortu')->default(false);
            $table->string('surat_peringatan', 10)->nullable(); // sp1/sp2/sp3
            $table->string('status_penyelesaian', 20)->default('proses'); // proses/selesai/dibebaskan
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['student_id', 'tanggal_kejadian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offenses');
    }
};
