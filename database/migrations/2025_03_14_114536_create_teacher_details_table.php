<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_details', function (Blueprint $table) {
            $table->id();
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('education_level')->nullable();
            $table->string('university')->nullable();
            $table->string('degree')->nullable();
            $table->string('major')->nullable();
            $table->year('graduation_year')->nullable();
            $table->string('certification')->nullable();
            $table->text('teaching_experience')->nullable();
            $table->text('biography')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->integer('children_count')->nullable();
            $table->string('social_media_facebook')->nullable();
            $table->string('social_media_twitter')->nullable();
            $table->string('social_media_linkedin')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('tax_id')->nullable();
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
        Schema::dropIfExists('teacher_details');
    }
}
