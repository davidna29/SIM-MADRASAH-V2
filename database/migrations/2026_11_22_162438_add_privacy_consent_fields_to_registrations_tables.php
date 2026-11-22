<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->timestamp('privacy_consent_at')->nullable();
            $table->string('privacy_consent_version', 20)->nullable();
        });

        Schema::table('mutasi_registrations', function (Blueprint $table) {
            $table->timestamp('privacy_consent_at')->nullable();
            $table->string('privacy_consent_version', 20)->nullable();
        });

        Schema::table('ppdb_interests', function (Blueprint $table) {
            $table->timestamp('privacy_consent_at')->nullable();
            $table->string('privacy_consent_version', 20)->nullable();
        });

        Schema::table('mutasi_interests', function (Blueprint $table) {
            $table->timestamp('privacy_consent_at')->nullable();
            $table->string('privacy_consent_version', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_registrations', function (Blueprint $table) {
            $table->dropColumn(['privacy_consent_at', 'privacy_consent_version']);
        });

        Schema::table('ppdb_interests', function (Blueprint $table) {
            $table->dropColumn(['privacy_consent_at', 'privacy_consent_version']);
        });

        Schema::table('mutasi_interests', function (Blueprint $table) {
            $table->dropColumn(['privacy_consent_at', 'privacy_consent_version']);
        });

        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->dropColumn(['privacy_consent_at', 'privacy_consent_version']);
        });
    }
};
