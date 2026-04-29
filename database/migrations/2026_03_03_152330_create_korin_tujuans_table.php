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
        Schema::create('korin_tujuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('korin_id')->constrained('korins')->onDelete('cascade');
            $table->foreignId('unit_tujuan_id')->constrained('units')->onDelete('cascade');
            $table->enum('status_baca', ['belum_dibaca', 'dibaca'])->default('belum_dibaca');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('korin_tujuans');
    }
};
