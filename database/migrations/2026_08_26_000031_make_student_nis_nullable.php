<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PPDB: NIS ditunda dari proses "Terima".
     * Student PPDB dibuat tanpa NIS, lalu diberi NIS massal di menu Generate NIS.
     * MySQL mengizinkan banyak NULL pada unique index, jadi unique tetap aman.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nis', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nis', 20)->change();
        });
    }
};
