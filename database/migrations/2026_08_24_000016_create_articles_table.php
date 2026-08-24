<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('summary', 300)->nullable();
            $table->longText('body');
            $table->string('category', 50)->nullable();
            $table->string('tags', 255)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->string('status', 20)->default('draft'); // draft/diajukan/review/revisi/disetujui/dijadwalkan/publish/arsip
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
