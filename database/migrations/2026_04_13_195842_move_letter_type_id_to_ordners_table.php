<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ordners', function (Blueprint $table) {
            $table->foreignId('letter_type_id')->nullable()->constrained('letter_types')->nullOnDelete();
        });

        Schema::table('archives', function (Blueprint $table) {
            $table->dropForeign(['letter_type_id']);
            $table->dropColumn('letter_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->foreignId('letter_type_id')->nullable()->constrained('letter_types')->cascadeOnDelete();
        });

        Schema::table('ordners', function (Blueprint $table) {
            $table->dropForeign(['letter_type_id']);
            $table->dropColumn('letter_type_id');
        });
    }
};
