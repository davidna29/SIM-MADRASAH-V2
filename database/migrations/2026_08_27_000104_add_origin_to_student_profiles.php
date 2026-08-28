<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sekolah asal (group G) — melengkapi snapshot profil agar tidak ada data hilang
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('origin_school', 100)->nullable()->after('previous_school');
            $table->string('origin_nsm', 12)->nullable()->after('origin_school');
            $table->string('origin_npsn', 8)->nullable()->after('origin_nsm');
            $table->string('origin_address', 255)->nullable()->after('origin_npsn');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['origin_school', 'origin_nsm', 'origin_npsn', 'origin_address']);
        });
    }
};
