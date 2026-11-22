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
        Schema::table('media_items', function (Blueprint $table) {
            $table->timestamp('publication_approved_at')->nullable()->after('sort_order');
            $table->foreignId('publication_approved_by')->nullable()->after('publication_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('publication_approved_by');
            $table->dropColumn('publication_approved_at');
        });
    }
};
