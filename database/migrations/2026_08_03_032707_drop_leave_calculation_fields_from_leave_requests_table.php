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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['leave_earned', 'deferred_leave', 'leave_debited', 'travelling_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedSmallInteger('leave_earned')->nullable()->after('phone_contact');
            $table->unsignedSmallInteger('deferred_leave')->nullable()->after('leave_earned');
            $table->unsignedSmallInteger('leave_debited')->nullable()->after('deferred_leave');
            $table->unsignedSmallInteger('travelling_time')->nullable()->after('leave_debited');
        });
    }
};
