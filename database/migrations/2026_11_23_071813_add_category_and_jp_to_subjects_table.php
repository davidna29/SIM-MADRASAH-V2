<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Kelompok mapel mengikuti struktur KMA 1503/2025 (keagamaan/umum/mulok).
            $table->string('category', 20)->nullable()->after('name');
            // Alokasi JP per tingkat (I..VI); null bila tidak diatur.
            $table->json('jp_per_grade')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['category', 'jp_per_grade']);
        });
    }
};
