<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ruangan & Laboratorium madrasah (PRD MOD-047/050)
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // R-001, R-002
            $table->string('name', 100);
            $table->string('type', 20)->default('ruangan'); // ruangan / laboratorium
            $table->string('building', 60)->nullable(); // gedung
            $table->string('floor', 20)->nullable(); // lantai
            $table->unsignedSmallInteger('capacity')->default(0);
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete(); // penanggung jawab
            $table->string('condition', 20)->default('baik'); // baik / rusak_ringan / rusak_berat / dalam_perbaikan
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
