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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('present');
            $table->enum('attendee_type', ['student', 'teacher'])->default('student');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Add unique constraint to prevent duplicate attendance records
            $table->unique(['user_id', 'date', 'attendee_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
