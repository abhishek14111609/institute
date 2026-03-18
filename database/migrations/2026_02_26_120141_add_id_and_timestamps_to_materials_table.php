<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite cannot add an autoincrement primary key through ALTER TABLE.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'id')) {
                $table->id()->first();
            }
            if (!Schema::hasColumn('materials', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'id')) {
                $table->dropColumn('id');
            }
            if (Schema::hasColumn('materials', 'created_at') || Schema::hasColumn('materials', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
