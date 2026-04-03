<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up any duplicate batches by keeping the latest one
        // and removing older duplicates
        DB::statement('
               DELETE FROM batches
               WHERE id NOT IN (
                   SELECT maxid FROM (
                       SELECT MAX(id) as maxid
                       FROM batches
                       GROUP BY school_id, subject_id, class_id, name
                   ) as temp
               )
           ');

        Schema::table('batches', function (Blueprint $table) {
            // Add unique constraint for batch name within school/subject/class combination
            // This prevents duplicate batches for the same subject and class within a school
            $table->unique(
                ['school_id', 'subject_id', 'class_id', 'name'],
                'unique_batch_per_subject_class'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropUnique('unique_batch_per_subject_class');
        });
    }
};
