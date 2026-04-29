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
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat', 100);
            $table->date('tanggal');
            $table->foreignId('pengirim_id')->constrained('pengirims')->onDelete('restrict')->onUpdate('cascade');
            $table->string('referensi', 100)->nullable();
            $table->string('tentang', 255)->nullable();
            $table->string('dokumen', 255)->nullable();
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->text('disposisi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_letters');
    }
};
