<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('archive_shelves', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode box, contoh: BOX-001
            $table->string('name');           // Nama box, contoh: Box Arsip Periode 2020
            $table->text('description')->nullable();
            $table->string('location')->nullable(); // Lokasi fisik rak
            $table->string('status')->default('active'); // active, full
            $table->foreignId('managed_by_unit_id')->nullable()->constrained('units')->nullOnDelete(); // Unit yg mengelola (SDM & Umum)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_shelves');
    }
};
