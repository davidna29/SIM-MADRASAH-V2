<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rapor terbit adalah snapshot permanen — tiap penerbitan membuat versi baru,
        // tidak pernah menimpa versi lama (PRD 7.2, 7.6).
        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('report_unique');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('version');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->unique(['student_id', 'academic_year_id', 'semester'], 'report_unique');
        });
    }
};
