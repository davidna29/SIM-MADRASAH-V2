<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // People — biodata inti (PRD 7.3): identitas vs akun dipisah
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('name', 100);
            $table->string('gender', 1)->default('L');
            $table->string('religion', 20)->default('Islam');
            $table->string('birth_place', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });

        // Organisasi & jabatan — role/jabatan/penugasan dipisah struktural (PRD 7.4)
        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 60);
            $table->timestamps();
        });

        // Employees — menghubungkan identitas ke peran pegawai
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organizational_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nip', 20)->nullable()->unique();
            $table->string('employee_status', 20)->default('honor'); // pns / pppk / honor
            $table->string('status', 20)->default('aktif'); // aktif / cuti / nonaktif
            $table->date('tmt')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Riwayat jabatan — tidak pernah menimpa (PRD 7.2)
        Schema::create('employee_position_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organizational_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->string('reason', 60)->nullable(); // pengangkatan / mutasi / promosi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_position_histories');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('organizational_units');
        Schema::dropIfExists('people');
    }
};
