<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
            $table->string('code', 30)->unique();
            $table->string('name', 120);
            $table->string('brand', 60)->nullable();
            $table->string('model', 60)->nullable();
            $table->string('serial_number', 60)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('unit', 20)->default('unit'); // unit / set / buah / pcs
            $table->string('condition', 20)->default('baik'); // baik / rusak_ringan / rusak_berat / hilang
            $table->string('location', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->unsignedInteger('purchase_price')->nullable(); // Rupiah
            $table->string('status', 20)->default('tersedia'); // tersedia / dipinjam / dalam_perawatan / tidak_aktif
            $table->string('photo', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('from_location', 100)->nullable();
            $table->string('to_location', 100);
            $table->unsignedInteger('quantity')->default(1);
            $table->date('mutation_date');
            $table->string('reason', 200)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('pending'); // pending / disetujui / ditolak
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('type', 20); // perawatan / perbaikan
            $table->text('description');
            $table->unsignedInteger('cost')->nullable(); // Rupiah
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('technician', 100)->nullable();
            $table->string('status', 20)->default('berlangsung'); // berlangsung / selesai
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_maintenances');
        Schema::dropIfExists('inventory_mutations');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
    }
};
