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
            $table->boolean('hod_recommended')->nullable()->after('travel_expense_assistance');
            $table->boolean('hod_relief_required')->nullable()->after('hod_recommended');
            $table->text('hod_comments')->nullable()->after('hod_relief_required');
            $table->foreignId('hod_reviewed_by')->nullable()->after('hod_comments')->constrained('users')->nullOnDelete();
            $table->timestamp('hod_reviewed_at')->nullable()->after('hod_reviewed_by');
            $table->foreignId('ps_reviewed_by')->nullable()->after('hod_reviewed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('ps_reviewed_at')->nullable()->after('ps_reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hod_reviewed_by');
            $table->dropConstrainedForeignId('ps_reviewed_by');
            $table->dropColumn(['hod_recommended', 'hod_relief_required', 'hod_comments', 'hod_reviewed_at', 'ps_reviewed_at']);
        });
    }
};
