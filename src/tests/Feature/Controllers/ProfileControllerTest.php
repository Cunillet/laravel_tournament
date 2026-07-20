<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_own_profile(): void
    {
        // Given
        $user = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->get(route('profile.show', $user));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Profile/Show'));
    }

    /** @test */
    public function it_blocks_viewing_other_profiles(): void
    {
        // Given
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->get(route('profile.show', $other));

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function it_shows_edit_form(): void
    {
        // Given
        $user = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->get(route('profile.edit', $user));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Profile/Edit'));
    }

    /** @test */
    public function it_blocks_editing_other_profiles(): void
    {
        // Given
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->get(route('profile.edit', $other));

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function it_updates_own_profile(): void
    {
        // Given
        $user = User::factory()->create([
            'nickname' => 'old_nick',
            'email' => 'old@example.com',
        ]);
        $this->actingAs($user);

        // When
        $response = $this->put(route('profile.update', $user), [
            'nickname' => 'new_nick',
            'email' => 'new@example.com',
            'current_password' => 'password',
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nickname' => 'new_nick',
            'email' => 'new@example.com',
        ]);
    }

    /** @test */
    public function it_updates_password(): void
    {
        // Given
        $user = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->put(route('profile.update-password', $user), [
            'current_password' => 'password',
            'password' => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ]);

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_requires_current_password_for_update(): void
    {
        // Given
        $user = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->put(route('profile.update', $user), [
            'nickname' => 'new_nick',
            'email' => $user->email,
            'current_password' => 'wrong-password',
        ]);

        // Then
        $response->assertSessionHasErrors(['current_password']);
    }
}
