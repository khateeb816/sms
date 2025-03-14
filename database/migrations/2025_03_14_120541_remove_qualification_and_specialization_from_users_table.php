<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveQualificationAndSpecializationFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove qualification and specialization fields from users table
            // as they are now in the teacher_details table
            if (Schema::hasColumn('users', 'qualification')) {
                $table->dropColumn('qualification');
            }

            if (Schema::hasColumn('users', 'specialization')) {
                $table->dropColumn('specialization');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back qualification and specialization fields if migration is rolled back
            if (!Schema::hasColumn('users', 'qualification')) {
                $table->string('qualification')->nullable();
            }

            if (!Schema::hasColumn('users', 'specialization')) {
                $table->string('specialization')->nullable();
            }
        });
    }
}
