<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            $table->string('external_reference', 100)->nullable()->unique()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            $table->dropUnique(['external_reference']);
            $table->dropColumn('external_reference');
        });
    }
};
