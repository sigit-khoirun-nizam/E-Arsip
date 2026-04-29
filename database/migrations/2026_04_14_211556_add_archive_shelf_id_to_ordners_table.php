<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordners', function (Blueprint $table) {
            $table->foreignId('archive_shelf_id')->nullable()->after('description')->constrained('archive_shelves')->nullOnDelete();
            $table->date('retention_expires_at')->nullable()->after('archive_shelf_id'); // Tanggal kadaluarsa retensi
        });
    }

    public function down(): void
    {
        Schema::table('ordners', function (Blueprint $table) {
            $table->dropForeign(['archive_shelf_id']);
            $table->dropColumn(['archive_shelf_id', 'retention_expires_at']);
        });
    }
};
