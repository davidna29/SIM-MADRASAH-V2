<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Akun siswa (role 'siswa') menunjuk ke satu record Student — dasar Portal Siswa
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('role')->constrained('students')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_student_id_unique');
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');
        });
    }
};
