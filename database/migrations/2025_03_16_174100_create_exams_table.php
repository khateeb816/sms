<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->string('subject');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->enum('type', ['first_term', 'second_term', 'third_term', 'final_term' , 'normal' , 'weekly' , 'monthly' , 'yearly'])->default('first_term');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
