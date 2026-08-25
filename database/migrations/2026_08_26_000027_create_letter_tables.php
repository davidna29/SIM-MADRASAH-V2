<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel kategori surat
        Schema::create('letter_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Tabel surat masuk/keluar
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['masuk', 'keluar']);
            $table->string('number')->nullable(); // Nomor surat (opsional untuk masuk, auto untuk keluar)
            $table->date('date');
            $table->string('from_to'); // Pengirim (masuk) / Penerima (keluar)
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('status', ['diterima', 'diproses', 'selesai', 'arsip'])->default('diterima');
            $table->enum('priority', ['biasa', 'penting', 'segera', 'rahasia'])->default('biasa');
            $table->string('category')->nullable(); // Kategori surat (undangan, pemberitahuan, dll)
            $table->string('disposition_to')->nullable(); // Disposisi ke
            $table->text('disposition_note')->nullable(); // Catatan disposisi
            $table->string('file_url')->nullable(); // URL file PDF lampiran
            $table->foreignId('recorded_by')->constrained('users');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->timestamps();

            // Index untuk pencarian
            $table->index(['type', 'status']);
            $table->index(['type', 'date']);
            $table->index(['from_to']);
            $table->index(['subject']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
        Schema::dropIfExists('letter_categories');
    }
};
