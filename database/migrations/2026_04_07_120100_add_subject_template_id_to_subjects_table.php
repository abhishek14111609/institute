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
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('subject_template_id')
                ->nullable()
                ->after('level_id')
                ->constrained('course_subject_templates')
                ->onDelete('set null');

            $table->index(['class_id', 'subject_template_id'], 'subjects_class_template_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('subjects_class_template_idx');
            $table->dropForeign(['subject_template_id']);
            $table->dropColumn('subject_template_id');
        });
    }
};
