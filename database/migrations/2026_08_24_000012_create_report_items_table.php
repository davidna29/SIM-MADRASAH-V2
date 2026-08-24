<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Detail nilai per mata pelajaran — rapor parent (1 per siswa+tahun+semester) memegang banyak item
        Schema::create('report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('subject_code', 10);
            $table->string('subject_name', 60);
            $table->foreignId('class_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('class_name', 20);
            $table->string('teacher_name', 100);
            $table->unsignedTinyInteger('score')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['report_id', 'subject_code'], 'report_item_unique');
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_items');
    }
};
