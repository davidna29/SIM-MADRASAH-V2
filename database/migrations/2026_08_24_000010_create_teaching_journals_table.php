<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jurnal mengajar — catatan pelaksanaan pembelajaran per penugasan (PRD MOD-024)
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_assignment_id')->constrained()->cascadeOnDelete();
            $table->date('journal_date');
            $table->unsignedTinyInteger('period_no')->nullable(); // jam ke- (opsional)
            $table->text('materi');
            $table->text('tujuan')->nullable();
            $table->string('metode', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->string('lampiran', 255)->nullable();
            $table->string('status', 10)->default('terisi'); // draft / terisi
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['teacher_assignment_id', 'journal_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_journals');
    }
};
