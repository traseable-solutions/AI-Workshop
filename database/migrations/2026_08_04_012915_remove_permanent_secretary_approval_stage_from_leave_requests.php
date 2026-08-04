<?php

use App\Models\LeaveRequest;
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
        // Any request still mid-chain (HOD recommended, awaiting a PS decision that will
        // no longer happen) becomes final now based on what the HOD already recommended.
        LeaveRequest::where('status', 'awaiting_ps')
            ->get()
            ->each(fn (LeaveRequest $leaveRequest) => $leaveRequest->update([
                'status' => $leaveRequest->hod_recommended ? 'approved' : 'rejected',
            ]));

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ps_reviewed_by');
            $table->dropColumn(['hod_recommended', 'ps_reviewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('hod_recommended')->nullable()->after('travel_expense_assistance');
            $table->foreignId('ps_reviewed_by')->nullable()->after('hod_reviewed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('ps_reviewed_at')->nullable()->after('ps_reviewed_by');
        });
    }
};
