<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sumber username yang dipakai untuk akun pegawai (NIP atau NIK dari people).
        Schema::table('employees', function (Blueprint $table) {
            $table->string('username_source', 10)->nullable()->after('nip');
        });

        // Kolom provisioning akun: wajib ganti password + saklar aktif/nonaktif.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(true)->after('student_id');
            $table->boolean('is_active')->default(true)->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('username_source');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['must_change_password', 'is_active']);
        });
    }
};
