<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('title', 200);
            $table->string('author', 120);
            $table->string('publisher', 120)->nullable();
            $table->smallInteger('year')->unsigned()->nullable();
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->string('isbn', 30)->nullable();
            $table->unsignedSmallInteger('total_qty')->default(1);
            $table->unsignedSmallInteger('available_qty')->default(1);
            $table->string('location', 100)->nullable(); // rak / lokasi fisik
            $table->string('cover_image', 255)->nullable();
            $table->boolean('is_ebook')->default(false);
            $table->string('ebook_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('tersedia'); // tersedia / tidak_aktif
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_type', 10); // siswa / pegawai
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('member_no', 20)->unique();
            $table->string('name', 100); // snapshot nama
            $table->string('status', 20)->default('aktif'); // aktif / nonaktif
            $table->date('joined_at');
            $table->timestamps();
        });

        Schema::create('library_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->string('status', 20)->default('dipinjam'); // dipinjam / dikembalikan / terlambat
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_loans');
        Schema::dropIfExists('library_members');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('library_categories');
    }
};
