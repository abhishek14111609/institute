<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('module', 50);
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'module', 'slug']);
            $table->index(['school_id', 'module', 'is_active']);
        });

        $now = now();

        if (Schema::hasTable('expenses')) {
            $expenseRows = DB::table('expenses')
                ->select('school_id', 'category')
                ->whereNotNull('school_id')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->get();

            foreach ($expenseRows as $row) {
                $name = trim((string) $row->category);
                if ($name === '') {
                    continue;
                }

                DB::table('category_types')->updateOrInsert(
                    [
                        'school_id' => (int) $row->school_id,
                        'module' => 'expense',
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'is_active' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }

        if (Schema::hasTable('inventory_items')) {
            $inventoryRows = DB::table('inventory_items')
                ->select('school_id', 'category')
                ->whereNotNull('school_id')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->get();

            foreach ($inventoryRows as $row) {
                $name = trim((string) $row->category);
                if ($name === '') {
                    continue;
                }

                DB::table('category_types')->updateOrInsert(
                    [
                        'school_id' => (int) $row->school_id,
                        'module' => 'inventory',
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'is_active' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_types');
    }
};