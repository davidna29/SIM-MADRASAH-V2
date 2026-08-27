<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periode ujian PPI kelas VI (munaqasah) — per tahun ajaran
        Schema::create('ppi_exam_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->string('judul', 150);
            $table->date('tanggal_setoran_mulai')->nullable();
            $table->date('tanggal_setoran_selesai')->nullable();
            $table->date('tanggal_ujian')->nullable();
            // draft | setup | berlangsung | selesai | diarsipkan
            $table->string('status', 20)->default('draft');
            $table->timestamp('config_locked_at')->nullable();
            // bobot dalam persen, total wajib 100 (divalidasi controller)
            $table->unsignedTinyInteger('bobot_p1')->default(25);
            $table->unsignedTinyInteger('bobot_p2')->default(25);
            $table->unsignedTinyInteger('bobot_p3')->default(25);
            $table->unsignedTinyInteger('bobot_hafalan')->default(25);
            // template teks (bisa diedit panitia per periode)
            $table->text('teks_mc')->nullable();
            $table->text('teks_ba')->nullable();
            $table->timestamps();
            $table->index(['academic_year_id', 'status']);
        });

        // Skala predikat per periode (dikunci saat berlangsung)
        Schema::create('ppi_exam_predicate_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->string('predikat', 10);
            $table->unsignedTinyInteger('nilai_min');
            $table->unsignedTinyInteger('nilai_max');
            $table->string('deskripsi', 255)->nullable();
            $table->boolean('is_tidak_lulus')->default(false);
            $table->unsignedSmallInteger('urutan');
            $table->timestamps();
            $table->unique(['exam_period_id', 'urutan']);
        });

        // Induk aspek penilaian, tiap induk di-assign ke penguji ke-1/2/3
        Schema::create('ppi_exam_aspect_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->string('kode', 10)->nullable();
            $table->string('nama', 150);
            $table->unsignedTinyInteger('penguji_urutan'); // 1|2|3
            $table->unsignedSmallInteger('urutan');
            $table->timestamps();
            $table->index(['exam_period_id', 'urutan']);
        });

        // Anak-item aspek
        Schema::create('ppi_exam_aspects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspect_category_id')->constrained('ppi_exam_aspect_categories')->cascadeOnDelete();
            $table->string('kode', 10)->nullable();
            $table->string('nama', 150);
            $table->unsignedSmallInteger('urutan');
            $table->timestamps();
        });

        // Materi setoran hafalan per periode (Fase 1)
        Schema::create('ppi_exam_hafalan_materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->unsignedSmallInteger('urutan');
            $table->timestamps();
        });

        // Ruang ujian per periode
        Schema::create('ppi_exam_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->timestamps();
        });

        // Penguji per ruang; 1 guru = maksimal 1 ruang per periode
        Schema::create('ppi_exam_examiners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->foreignId('exam_room_id')->constrained('ppi_exam_rooms')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('urutan'); // 1|2|3
            $table->timestamps();
            $table->unique(['exam_period_id', 'employee_id']);
            $table->unique(['exam_room_id', 'urutan']);
        });

        // Grup setoran + pembimbing
        Schema::create('ppi_exam_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->foreignId('pembimbing_employee_id')->constrained('employees')->restrictOnDelete();
            $table->timestamps();
        });

        // Peserta (siswa kelas VI) — snapshot rombel asal utk Rank Lokal + cache hitungan
        Schema::create('ppi_exam_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('exam_room_id')->constrained('ppi_exam_rooms')->restrictOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('ppi_exam_groups')->restrictOnDelete();
            $table->foreignId('class_group_id')->constrained('class_groups')->restrictOnDelete();
            $table->unsignedSmallInteger('no_urut');
            $table->string('status', 20)->default('aktif');
            // cache hitungan (dihitung ulang oleh PpiExamScoringService)
            $table->unsignedSmallInteger('jumlah_p1')->nullable();
            $table->decimal('rata_p1', 5, 2)->nullable();
            $table->unsignedSmallInteger('jumlah_p2')->nullable();
            $table->decimal('rata_p2', 5, 2)->nullable();
            $table->unsignedSmallInteger('jumlah_p3')->nullable();
            $table->decimal('rata_p3', 5, 2)->nullable();
            $table->unsignedSmallInteger('jumlah_ujian_lisan')->nullable();
            $table->decimal('rata_ujian_lisan', 5, 2)->nullable();
            $table->decimal('rata_hafalan', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->foreignId('predicate_scale_id')->nullable()->constrained('ppi_exam_predicate_scales')->nullOnDelete();
            $table->boolean('status_lulus')->nullable();
            $table->unsignedSmallInteger('rank_total')->nullable();
            $table->unsignedSmallInteger('rank_lokal')->nullable();
            $table->timestamps();
            $table->unique(['exam_period_id', 'student_id']);
        });

        // Nilai ujian lisan (per aspek, diinput penguji)
        Schema::create('ppi_exam_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('ppi_exam_participants')->cascadeOnDelete();
            $table->foreignId('aspect_id')->constrained('ppi_exam_aspects')->restrictOnDelete();
            $table->unsignedTinyInteger('nilai');
            $table->foreignId('examiner_employee_id')->constrained('employees')->restrictOnDelete();
            $table->timestamp('input_at')->nullable();
            $table->timestamps();
            $table->unique(['participant_id', 'aspect_id']);
        });

        // Nilai setoran hafalan (per materi, diinput pembimbing)
        Schema::create('ppi_exam_hafalan_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('ppi_exam_participants')->cascadeOnDelete();
            $table->foreignId('hafalan_materi_id')->constrained('ppi_exam_hafalan_materi')->restrictOnDelete();
            $table->unsignedTinyInteger('nilai');
            $table->date('tanggal_setor')->nullable();
            $table->foreignId('dinilai_oleh_employee_id')->constrained('employees')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['participant_id', 'hafalan_materi_id']);
        });

        // Arsip tahun lalu (import Excel) — rekap akhir saja, tanpa FK siswa
        Schema::create('ppi_exam_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_period_id')->constrained('ppi_exam_periods')->cascadeOnDelete();
            $table->string('nisn', 20)->nullable();
            $table->string('nama_siswa', 150);
            $table->decimal('rata_p1', 5, 2)->nullable();
            $table->decimal('rata_p2', 5, 2)->nullable();
            $table->decimal('rata_p3', 5, 2)->nullable();
            $table->decimal('rata_hafalan', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('predikat', 10)->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->string('status_lulus', 10)->nullable(); // Lulus | Tidak Lulus
            $table->string('rank', 20)->nullable();
            $table->string('rombel', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppi_exam_archives');
        Schema::dropIfExists('ppi_exam_hafalan_scores');
        Schema::dropIfExists('ppi_exam_scores');
        Schema::dropIfExists('ppi_exam_participants');
        Schema::dropIfExists('ppi_exam_groups');
        Schema::dropIfExists('ppi_exam_examiners');
        Schema::dropIfExists('ppi_exam_rooms');
        Schema::dropIfExists('ppi_exam_hafalan_materi');
        Schema::dropIfExists('ppi_exam_aspects');
        Schema::dropIfExists('ppi_exam_aspect_categories');
        Schema::dropIfExists('ppi_exam_predicate_scales');
        Schema::dropIfExists('ppi_exam_periods');
    }
};
