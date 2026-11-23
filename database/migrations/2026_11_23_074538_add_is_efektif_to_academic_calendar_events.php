<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_calendar_events', function (Blueprint $table) {
            // Kegiatan seperti PLM/asesmen/rapor tetap berlangsung tetapi tidak
            // dihitung sebagai hari efektif pembelajaran.
            $table->boolean('is_efektif')->default(true)->after('is_libur');
        });
    }

    public function down(): void
    {
        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->dropColumn('is_efektif');
        });
    }
};
