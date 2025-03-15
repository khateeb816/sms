<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type'); // 'admin', 'teacher', 'parent', 'student'
            $table->unsignedBigInteger('recipient_id')->nullable(); // Null for broadcast messages
            $table->string('recipient_type')->nullable(); // 'admin', 'teacher', 'parent', 'student', 'all'
            $table->string('subject');
            $table->text('message');
            $table->string('message_type'); // 'alert', 'warning', 'complaint', 'general'
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_broadcast')->default(false); // For messages sent to all users of a type
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
