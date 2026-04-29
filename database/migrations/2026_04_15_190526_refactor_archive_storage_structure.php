<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Rename archive_shelves to archive_racks
        if (Schema::hasTable('archive_shelves') && !Schema::hasTable('archive_racks')) {
            Schema::rename('archive_shelves', 'archive_racks');
        }

        // 2. Adjust archive_racks columns
        Schema::table('archive_racks', function (Blueprint $table) {
            if (!Schema::hasColumn('archive_racks', 'capacity')) {
                $table->integer('capacity')->nullable()->after('location');
            }
            if (!Schema::hasColumn('archive_racks', 'managed_by_unit_id')) {
                $table->foreignId('managed_by_unit_id')->nullable()->constrained('units')->nullOnDelete()->after('status');
            }
        });

        // 3. Create archive_boxes table
        if (!Schema::hasTable('archive_boxes')) {
            Schema::create('archive_boxes', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('archive_rack_id')->nullable()->constrained('archive_racks')->nullOnDelete();
                $table->string('status')->default('active');
                $table->foreignId('managed_by_unit_id')->nullable()->constrained('units')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 4. Update ordners
        Schema::table('ordners', function (Blueprint $table) {
            if (Schema::hasColumn('ordners', 'archive_shelf_id') && !Schema::hasColumn('ordners', 'archive_box_id')) {
                try {
                    $table->dropForeign(['archive_shelf_id']);
                } catch (\Exception $e) {}
                
                $table->renameColumn('archive_shelf_id', 'archive_box_id');
            } elseif (!Schema::hasColumn('ordners', 'archive_box_id')) {
                $table->foreignId('archive_box_id')->nullable();
            }

            if (!Schema::hasColumn('ordners', 'retention_expires_at')) {
                $table->date('retention_expires_at')->nullable()->after('status');
            }
        });

        // Migrate Data safely: Create a default box for every rack, and map ordners to this box
        $racks = \Illuminate\Support\Facades\DB::table('archive_racks')->get();
        foreach ($racks as $rack) {
            // check if a box already exists for this rack to avoid duplicates on re-run
            $box = \Illuminate\Support\Facades\DB::table('archive_boxes')
                ->where('archive_rack_id', $rack->id)
                ->first();

            if (!$box) {
                $boxId = \Illuminate\Support\Facades\DB::table('archive_boxes')->insertGetId([
                    'code' => $rack->code . '-B1',
                    'name' => 'Box 1 - ' . $rack->name,
                    'archive_rack_id' => $rack->id,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $boxId = $box->id;
            }

            // At this point in time, archive_box_id in ordners actually contains the OLD archive_shelf_id (rack ID).
            // We need to update ordners where archive_box_id == $rack->id to be $boxId.
            \Illuminate\Support\Facades\DB::table('ordners')
                ->where('archive_box_id', $rack->id)
                ->update(['archive_box_id' => $boxId]);
        }

        // Clean up any remaining ordners that somehow have invalid box ids (set to null so constraint doesn't fail)
        $validBoxIds = \Illuminate\Support\Facades\DB::table('archive_boxes')->pluck('id')->toArray();
        if (!empty($validBoxIds)) {
            \Illuminate\Support\Facades\DB::table('ordners')
                ->whereNotNull('archive_box_id')
                ->whereNotIn('archive_box_id', $validBoxIds)
                ->update(['archive_box_id' => null]);
        } else {
            \Illuminate\Support\Facades\DB::table('ordners')->update(['archive_box_id' => null]);
        }

        // Finally, add constraints to re-added or renamed column
        Schema::table('ordners', function (Blueprint $table) {
             if (Schema::hasColumn('ordners', 'archive_box_id')) {
                 try {
                     $table->foreign('archive_box_id')->references('id')->on('archive_boxes')->nullOnDelete();
                 } catch (\Exception $e) {}
             }
        });
    }

    public function down(): void
    {
        Schema::table('ordners', function (Blueprint $table) {
            if (Schema::hasColumn('ordners', 'archive_box_id')) {
                try {
                    $table->dropForeign(['archive_box_id']);
                } catch (\Exception $e) {}
                $table->renameColumn('archive_box_id', 'archive_shelf_id');
            }
        });

        Schema::dropIfExists('archive_boxes');

        Schema::table('archive_racks', function (Blueprint $table) {
            $table->dropForeign(['managed_by_unit_id']);
            $table->dropColumn(['capacity', 'managed_by_unit_id']);
        });

        Schema::rename('archive_racks', 'archive_shelves');
    }
};
