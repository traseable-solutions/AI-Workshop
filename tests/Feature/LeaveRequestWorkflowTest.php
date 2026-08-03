<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function makeHierarchy(): array
    {
        $ps = User::factory()->create(['role' => 'permanent_secretary']);
        $hod = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee', 'manager_id' => $hod->id, 'level' => 3]);
        $stranger = User::factory()->create(['role' => 'employee']);

        return compact('ps', 'hod', 'employee', 'stranger');
    }

    public function test_employee_can_submit_annual_leave_application_with_form_fields(): void
    {
        ['employee' => $employee] = $this->makeHierarchy();

        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'annual',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
            'level' => 3,
            'package_amount' => 8000,
            'leave_destination' => 'Miami',
            'leave_address' => '123 Beach Rd',
            'phone_contact' => '555-1234',
            'travel_expense_assistance' => 500,
        ]);

        $response->assertRedirect(route('leave-requests.index'));

        $leaveRequest = LeaveRequest::first();
        $this->assertSame('pending', $leaveRequest->status);
        $this->assertSame('Miami', $leaveRequest->leave_destination);
        $this->assertSame(8500, $leaveRequest->totalPackageAmount());
    }

    public function test_hod_can_recommend_and_forward_to_permanent_secretary(): void
    {
        ['hod' => $hod, 'employee' => $employee] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'status' => 'pending',
        ]);

        $response = $this->actingAs($hod)->post("/leave-requests/{$leaveRequest->id}/recommend", [
            'hod_recommended' => '1',
            'hod_relief_required' => '0',
            'hod_comments' => 'Approved for travel.',
        ]);

        $response->assertRedirect();
        $leaveRequest->refresh();
        $this->assertSame('awaiting_ps', $leaveRequest->status);
        $this->assertTrue($leaveRequest->hod_recommended);
        $this->assertFalse($leaveRequest->hod_relief_required);
        $this->assertSame($hod->id, $leaveRequest->hod_reviewed_by);
        $this->assertNotNull($leaveRequest->hod_reviewed_at);
    }

    public function test_non_hod_cannot_recommend(): void
    {
        ['stranger' => $stranger, 'employee' => $employee] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'status' => 'pending',
        ]);

        $this->actingAs($stranger)->post("/leave-requests/{$leaveRequest->id}/recommend", [
            'hod_recommended' => '1',
            'hod_relief_required' => '0',
        ])->assertForbidden();
    }

    public function test_cannot_recommend_a_request_that_already_moved_past_hod(): void
    {
        ['hod' => $hod, 'employee' => $employee] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'status' => 'awaiting_ps',
        ]);

        $this->actingAs($hod)->post("/leave-requests/{$leaveRequest->id}/recommend", [
            'hod_recommended' => '1',
            'hod_relief_required' => '0',
        ])->assertStatus(409);
    }

    public function test_permanent_secretary_can_approve_a_forwarded_request(): void
    {
        ['ps' => $ps, 'hod' => $hod, 'employee' => $employee] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10',
            'status' => 'awaiting_ps', 'hod_reviewed_by' => $hod->id, 'hod_reviewed_at' => now(),
        ]);

        $response = $this->actingAs($ps)->post("/leave-requests/{$leaveRequest->id}/decide", [
            'decision' => 'approved',
        ]);

        $response->assertRedirect();
        $leaveRequest->refresh();
        $this->assertSame('approved', $leaveRequest->status);
        $this->assertSame($ps->id, $leaveRequest->ps_reviewed_by);
        $this->assertNotNull($leaveRequest->ps_reviewed_at);
    }

    public function test_non_permanent_secretary_cannot_decide(): void
    {
        ['hod' => $hod, 'employee' => $employee] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10',
            'status' => 'awaiting_ps', 'hod_reviewed_by' => $hod->id, 'hod_reviewed_at' => now(),
        ]);

        $this->actingAs($hod)->post("/leave-requests/{$leaveRequest->id}/decide", [
            'decision' => 'approved',
        ])->assertForbidden();
    }

    public function test_permanent_secretary_cannot_decide_a_request_still_pending_with_hod(): void
    {
        ['ps' => $ps, 'employee' => $employee] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'status' => 'pending',
        ]);

        $this->actingAs($ps)->post("/leave-requests/{$leaveRequest->id}/decide", [
            'decision' => 'approved',
        ])->assertStatus(409);
    }

    public function test_ps_queue_only_shows_requests_forwarded_by_hod(): void
    {
        ['ps' => $ps, 'hod' => $hod, 'employee' => $employee] = $this->makeHierarchy();

        $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'status' => 'pending',
        ]);

        $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-10-01', 'end_date' => '2026-10-10',
            'status' => 'awaiting_ps', 'hod_reviewed_by' => $hod->id, 'hod_reviewed_at' => now(),
        ]);

        $response = $this->actingAs($ps)->get('/ps-requests');

        $response->assertOk();
        $response->assertSee('2026-10-01');
        $response->assertDontSee('2026-09-01');
    }

    public function test_show_page_is_visible_to_owner_hod_and_ps_but_not_a_stranger(): void
    {
        ['ps' => $ps, 'hod' => $hod, 'employee' => $employee, 'stranger' => $stranger] = $this->makeHierarchy();

        $leaveRequest = $employee->leaveRequests()->create([
            'type' => 'annual', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'status' => 'pending',
        ]);

        $this->actingAs($employee)->get("/leave-requests/{$leaveRequest->id}")->assertOk();
        $this->actingAs($hod)->get("/leave-requests/{$leaveRequest->id}")->assertOk();
        $this->actingAs($ps)->get("/leave-requests/{$leaveRequest->id}")->assertOk();
        $this->actingAs($stranger)->get("/leave-requests/{$leaveRequest->id}")->assertForbidden();
    }
}
