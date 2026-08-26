<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fitur Generate NIS dihapus dari PPDB (NIS & kelas diisi di Data Siswa).
     * Tabel nis_counters tidak lagi terpakai, maka dihapus.
     */
    public function up(): void
    {
        Schema::dropIfExists('nis_counters');
    }

    public function down(): void
    {
        Schema::create('nis_counters', function ($table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique('academic_year_id');
        });
    }
};
