<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_their_profile(): void
    {
        $user = User::factory()->create([
            'tpf' => 'TPF-1001',
            'position' => 'Clerical Officer',
            'duty_station' => 'Head Office',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/profile');

        $response->assertOk();
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tpf' => 'TPF-1001',
            'position' => 'Clerical Officer',
            'duty_station' => 'Head Office',
        ]);
        $response->assertJsonMissing(['password']);
    }

    public function test_guest_cannot_view_profile(): void
    {
        $this->getJson('/api/profile')->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_their_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'tpf' => 'TPF-2002',
            'position' => 'Senior Officer',
            'duty_station' => 'Regional Office',
        ]);

        $response->assertOk();
        $response->assertJson([
            'tpf' => 'TPF-2002',
            'position' => 'Senior Officer',
            'duty_station' => 'Regional Office',
        ]);

        $this->assertSame('TPF-2002', $user->fresh()->tpf);
    }

    public function test_profile_update_validates_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/profile', [
            'name' => $user->name,
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }
}
