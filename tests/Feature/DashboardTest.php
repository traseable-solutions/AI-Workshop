<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_staff_currently_on_leave(): void
    {
        $viewer = User::factory()->create();

        $onLeave = User::factory()->create(['name' => 'Ola OnLeave']);
        LeaveRequest::create([
            'user_id' => $onLeave->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today()->subDay(),
            'end_date' => Carbon::today()->addDay(),
        ]);

        $notOnLeave = User::factory()->create(['name' => 'Nadia NotOnLeave']);
        LeaveRequest::create([
            'user_id' => $notOnLeave->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(6),
        ]);

        $pending = User::factory()->create(['name' => 'Peter Pending']);
        LeaveRequest::create([
            'user_id' => $pending->id,
            'type' => 'annual',
            'status' => 'pending',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
        ]);

        $response = $this->actingAs($viewer)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Ola OnLeave');
        $response->assertDontSee('Peter Pending');

        $onLeaveToday = $response->viewData('onLeaveToday')->pluck('name');
        $this->assertTrue($onLeaveToday->contains('Ola OnLeave'));
        $this->assertFalse($onLeaveToday->contains('Nadia NotOnLeave'));
        $this->assertFalse($onLeaveToday->contains('Peter Pending'));
    }

    public function test_dashboard_lists_upcoming_approved_leave_chronologically(): void
    {
        $viewer = User::factory()->create();

        $later = User::factory()->create(['name' => 'Lucy Later']);
        LeaveRequest::create([
            'user_id' => $later->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today()->addDays(10),
            'end_date' => Carbon::today()->addDays(12),
        ]);

        $soon = User::factory()->create(['name' => 'Sam Soon']);
        LeaveRequest::create([
            'user_id' => $soon->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today()->addDays(2),
            'end_date' => Carbon::today()->addDays(3),
        ]);

        $pending = User::factory()->create(['name' => 'Peter Pending']);
        LeaveRequest::create([
            'user_id' => $pending->id,
            'type' => 'annual',
            'status' => 'pending',
            'start_date' => Carbon::today()->addDays(1),
            'end_date' => Carbon::today()->addDays(1),
        ]);

        $response = $this->actingAs($viewer)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('Peter Pending');

        $upcoming = $response->viewData('upcomingLeave')->pluck('user.name');
        $this->assertSame(['Sam Soon', 'Lucy Later'], $upcoming->values()->all());
    }

    public function test_dashboard_breaks_down_leave_by_type_and_status_including_zero_counts(): void
    {
        $viewer = User::factory()->create();

        $annualUser = User::factory()->create();
        LeaveRequest::create([
            'user_id' => $annualUser->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
        ]);

        $sickUser = User::factory()->create();
        LeaveRequest::create([
            'user_id' => $sickUser->id,
            'type' => 'sick',
            'status' => 'pending',
            'start_date' => Carbon::today()->addDay(),
            'end_date' => Carbon::today()->addDay(),
        ]);

        $response = $this->actingAs($viewer)->get('/dashboard');

        $response->assertOk();

        $leaveByType = $response->viewData('leaveByType');
        $this->assertSame(1, $leaveByType->get('annual'));
        $this->assertSame(1, $leaveByType->get('sick'));
        $this->assertSame(0, $leaveByType->get('unpaid'));

        $leaveByStatus = $response->viewData('leaveByStatus');
        $this->assertSame(1, $leaveByStatus->get('approved'));
        $this->assertSame(1, $leaveByStatus->get('pending'));
        $this->assertSame(0, $leaveByStatus->get('awaiting_ps'));
        $this->assertSame(0, $leaveByStatus->get('rejected'));
    }

    public function test_dashboard_calendar_marks_today_with_the_approved_leave_count(): void
    {
        $viewer = User::factory()->create();

        $onLeave = User::factory()->create(['name' => 'Ola OnLeave']);
        LeaveRequest::create([
            'user_id' => $onLeave->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
        ]);

        $response = $this->actingAs($viewer)->get('/dashboard');

        $response->assertOk();

        $today = $response->viewData('calendarDays')->firstWhere('isToday', true);
        $this->assertNotNull($today);
        $this->assertTrue($today['users']->pluck('name')->contains('Ola OnLeave'));
    }
}
