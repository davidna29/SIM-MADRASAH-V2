<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pendaftaran siswa pindah masuk (mutasi) — paralel PPDB
        Schema::create('mutasi_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no', 20)->unique(); // MUT-YYYY-NNN
            $table->enum('status', ['draft', 'submitted', 'accepted', 'rejected'])->default('draft');
            $table->string('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // A. Identitas Siswa
            $table->string('name', 100);
            $table->string('nik', 16);
            $table->string('nisn', 10)->nullable();
            $table->string('nis_asal', 20)->nullable();
            $table->string('gender', 1);
            $table->string('religion', 20)->default('Islam');
            $table->string('birth_place', 60)->nullable();
            $table->date('birth_date')->nullable();

            // Asal sekolah / madrasah
            $table->string('origin_school', 100)->nullable();
            $table->string('origin_nsm', 12)->nullable();
            $table->string('origin_npsn', 8)->nullable();
            $table->string('origin_address', 255)->nullable();
            $table->string('kelas_asal', 20)->nullable();

            // Tujuan mutasi
            $table->string('kelas_tujuan', 20)->nullable();
            $table->text('alasan_pindah')->nullable();
            $table->date('tanggal_mutasi')->nullable();

            // Alamat tinggal
            $table->string('address', 255)->nullable();
            $table->string('province', 60)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('district', 60)->nullable();
            $table->string('village', 60)->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->string('student_phone', 20)->nullable();
            $table->string('student_email', 100)->nullable();

            // Orang tua / wali (ringkas)
            $table->string('father_name', 100)->nullable();
            $table->string('father_nik', 16)->nullable();
            $table->string('father_job', 30)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_nik', 16)->nullable();
            $table->string('mother_job', 30)->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_nik', 16)->nullable();
            $table->string('guardian_phone', 20)->nullable();

            // Dokumen (link Google Drive)
            $table->string('scanned_rekomendasi', 500)->nullable(); // Surat Rekomendasi Madrasah
            $table->string('scanned_rapor', 500)->nullable();
            $table->string('scanned_kk', 500)->nullable();
            $table->string('scanned_akta', 500)->nullable();
            $table->string('scanned_photo', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_registrations');
    }
};
