<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nouveau Nom',
                'email' => 'nouveau@example.com',
                'phone' => '0123456789',
                'bio' => 'Nouvelle bio',
                'level' => 'intermediate',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nouveau Nom',
            'email' => 'nouveau@example.com',
            'phone' => '0123456789',
            'bio' => 'Nouvelle bio',
            'level' => 'intermediate',
        ]);
    }

    public function test_tutor_can_update_hourly_rate(): void
    {
        $tutor = User::factory()->create(['role' => 'tutor']);

        $response = $this->actingAs($tutor)
            ->post(route('profile.hourly-rate'), [
                'hourly_rate' => 25.50,
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $tutor->id,
            'hourly_rate' => 25.50,
        ]);
    }

    public function test_tutor_can_update_availability(): void
    {
        $tutor = User::factory()->create([
            'role' => 'tutor',
            'is_available' => true,
        ]);

        $response = $this->actingAs($tutor)
            ->post(route('profile.availability'), [
                'is_available' => false,
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $tutor->id,
            'is_available' => false,
        ]);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'level' => $user->level,
                'avatar' => $file,
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_cannot_access_private_profile_without_permission(): void
    {
        $user1 = User::factory()->create(['is_public_profile' => false]);
        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)
            ->get(route('profile.show', $user1));

        $response->assertStatus(403);
    }

    public function test_user_can_access_public_profile(): void
    {
        $user = User::factory()->create(['is_public_profile' => true]);

        $response = $this->get(route('profile.show', $user));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_user_can_access_own_profile(): void
    {
        $user = User::factory()->create(['is_public_profile' => false]);

        $response = $this->actingAs($user)
            ->get(route('profile.show', $user));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_admin_can_access_any_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['is_public_profile' => false]);

        $response = $this->actingAs($admin)
            ->get(route('profile.show', $user));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_profile_validation_works(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => '',
                'email' => 'invalid-email',
                'level' => 'invalid-level',
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'level']);
    }

    public function test_tutor_skills_validation(): void
    {
        $tutor = User::factory()->create(['role' => 'tutor']);

        $response = $this->actingAs($tutor)
            ->patch(route('profile.update'), [
                'name' => $tutor->name,
                'email' => $tutor->email,
                'level' => $tutor->level,
                'skills' => ['invalid_skill'],
            ]);

        $response->assertSessionHasErrors(['skills.*']);
    }
}
