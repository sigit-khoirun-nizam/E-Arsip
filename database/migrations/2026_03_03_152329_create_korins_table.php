<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('korins', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 100);
            $table->date('tanggal_surat');
            $table->string('perihal', 255);
            $table->text('isi');
            $table->foreignId('unit_pengirim_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['Baru', 'Pending', 'Selesai', 'Ditolak'])->default('Baru');
            $table->string('file_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('korins');
    }
};
