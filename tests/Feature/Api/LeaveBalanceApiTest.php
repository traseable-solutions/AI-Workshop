<?php

namespace Tests\Feature\Api;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LeaveBalanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_remaining_balance_and_package_amount(): void
    {
        $user = User::factory()->create(['level' => 3]);

        LeaveRequest::create([
            'user_id' => $user->id,
            'type' => 'annual',
            'status' => 'approved',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(4),
        ]);

        LeaveRequest::create([
            'user_id' => $user->id,
            'type' => 'annual',
            'status' => 'pending',
            'start_date' => Carbon::today()->addDays(10),
            'end_date' => Carbon::today()->addDays(11),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/leave-balance');

        $response->assertOk();
        $response->assertExactJson([
            'balance' => User::ANNUAL_LEAVE_DAYS - 5,
            'leave_package_amount' => 8000,
        ]);
    }

    public function test_guest_cannot_view_leave_balance(): void
    {
        $this->getJson('/api/leave-balance')->assertUnauthorized();
    }
}
