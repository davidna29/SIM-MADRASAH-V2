<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_component_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('score_id')->constrained()->cascadeOnDelete();
            $table->foreignId('score_component_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('value')->nullable();
            $table->timestamps();
            $table->unique(['score_id', 'score_component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_component_values');
    }
};
