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
            $table->string('leave_destination')->nullable()->after('package_amount');
            $table->string('leave_address')->nullable()->after('leave_destination');
            $table->string('phone_contact')->nullable()->after('leave_address');
            $table->unsignedSmallInteger('leave_earned')->nullable()->after('phone_contact');
            $table->unsignedSmallInteger('deferred_leave')->nullable()->after('leave_earned');
            $table->unsignedSmallInteger('leave_debited')->nullable()->after('deferred_leave');
            $table->unsignedSmallInteger('travelling_time')->nullable()->after('leave_debited');
            $table->unsignedInteger('travel_expense_assistance')->nullable()->after('travelling_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'leave_destination',
                'leave_address',
                'phone_contact',
                'leave_earned',
                'deferred_leave',
                'leave_debited',
                'travelling_time',
                'travel_expense_assistance',
            ]);
        });
    }
};
