<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Urutan tampil mapel — menentukan posisi pada rapor / laporan nilai
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('name');
        });

        // Isi urutan awal sesuai id
        DB::statement('UPDATE subjects SET sort_order = id');
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
