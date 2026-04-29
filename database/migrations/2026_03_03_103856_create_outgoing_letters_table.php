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
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_surat', 100);
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->string('kepada', 150);
            $table->string('perihal', 255);
            $table->string('referensi', 100)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_letters');
    }
};
