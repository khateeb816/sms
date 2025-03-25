<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datesheets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->enum('term', ['first', 'second', 'third', 'final'])->default('first');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'published', 'completed'])->default('draft');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create pivot table for datesheet_exam
        Schema::create('datesheet_exam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datesheet_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datesheet_exam');
        Schema::dropIfExists('datesheets');
    }
}
