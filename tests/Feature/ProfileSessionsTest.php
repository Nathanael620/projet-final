<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_sessions_route_requires_authentication(): void
    {
        $response = $this->get('/profile/sessions');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_profile_sessions(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/profile/sessions');
        
        $response->assertStatus(200);
        $response->assertViewIs('profile.sessions');
    }

    public function test_profile_sessions_route_name_works(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('profile.sessions'));
        
        $response->assertStatus(200);
    }
}
