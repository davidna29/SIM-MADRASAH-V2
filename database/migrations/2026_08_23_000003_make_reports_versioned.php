<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rapor terbit adalah snapshot permanen — tiap penerbitan membuat versi baru,
        // tidak pernah menimpa versi lama (PRD 7.2, 7.6).
        // Di MySQL/InnoDB, index unik report_unique dibutuhkan oleh foreign key
        // reports_student_id_foreign, jadi foreign key harus dilepas dulu.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
                $table->dropUnique('report_unique');
            });
        } else {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropUnique('report_unique');
            });
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('status');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('version');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->unique(['student_id', 'academic_year_id', 'semester'], 'report_unique');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            });
        }
    }
};
