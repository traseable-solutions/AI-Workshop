<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_download_the_user_guide_pdf(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user-guide');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename=leave-management-user-guide.pdf');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/user-guide')->assertRedirect('/login');
    }
}
