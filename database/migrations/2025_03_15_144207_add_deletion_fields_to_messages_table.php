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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('deleted_by_sender')->default(false)->after('is_broadcast');
            $table->boolean('deleted_by_recipient')->default(false)->after('deleted_by_sender');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by_recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['deleted_by_sender', 'deleted_by_recipient', 'deleted_at']);
        });
    }
};
