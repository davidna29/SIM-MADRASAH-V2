<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mutasi siswa keluar — pencatatan siswa pindah keluar madrasah.
        // Saat record dibuat, status enrollment aktif tahun berjalan diubah menjadi 'keluar'
        // (dilakukan oleh MutasiKeluarService, bukan di sini).
        Schema::create('student_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal_mutasi');
            $table->string('sekolah_tujuan', 100);
            $table->string('tujuan_nsm', 12)->nullable();
            $table->string('tujuan_npsn', 8)->nullable();
            $table->string('alasan_pindah', 30);
            $table->text('keterangan')->nullable();
            $table->string('no_surat', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_mutations');
    }
};
