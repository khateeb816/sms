<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToExamTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add specific performance-critical indexes only
        Schema::table('exams', function (Blueprint $table) {
            // These are likely already indexed as they're foreign keys
            // $table->index('teacher_id');
            // $table->index('class_id');

            // Add indexes for common filters
            $table->index('subject');
            $table->index('status');
            $table->index('exam_date');
        });

        Schema::table('exam_results', function (Blueprint $table) {
            // Only add non-foreign key indexes to avoid conflicts
            $table->index('is_passed');
            $table->index('grade');
        });

        Schema::table('datesheets', function (Blueprint $table) {
            $table->index('term');
            $table->index('status');
            $table->index('is_result_published');
            $table->index(['start_date', 'end_date']);
        });

        // Add indexes to the datesheet_exam pivot table
        Schema::table('datesheet_exam', function (Blueprint $table) {
            $table->index(['datesheet_id', 'exam_id']);
        });

        // Add indexes to class_student table for better student filtering
        Schema::table('class_student', function (Blueprint $table) {
            $table->index(['class_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We'll skip the down migration to avoid issues with foreign keys
        // If indexes need to be removed, it's better to do it manually
    }
}
