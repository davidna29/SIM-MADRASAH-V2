<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MOD-020 Kalender Pendidikan — periode semester dengan snapshot
        // pekan/hari efektif (angka dapat dihitung ulang dari kegiatan).
        Schema::create('academic_calendar_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('semester', 10);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('effective_weeks')->nullable();
            $table->unsignedSmallInteger('effective_days')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'semester'], 'calendar_period_unique');
        });

        Schema::create('academic_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('category', 20)->default('kegiatan');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_libur')->default(false);
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['academic_year_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_events');
        Schema::dropIfExists('academic_calendar_periods');
    }
};
