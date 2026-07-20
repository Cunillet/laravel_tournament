<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_login_form(): void
    {
        // When
        $response = $this->get(route('login'));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/Login'));
    }

    /** @test */
    public function it_shows_the_register_form(): void
    {
        // When
        $response = $this->get(route('register'));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/Register'));
    }

    /** @test */
    public function it_registers_a_new_user(): void
    {
        // Given
        $data = [
            'nickname' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ];

        // When
        $response = $this->post(route('register'), $data);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'nickname' => 'newuser',
        ]);
        $this->assertAuthenticated();
    }

    /** @test */
    public function it_validates_registration_input(): void
    {
        // When
        $response = $this->post(route('register'), [
            'nickname' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'not-matching',
        ]);

        // Then
        $response->assertSessionHasErrors(['nickname', 'email', 'password']);
    }

    /** @test */
    public function it_logs_in_an_existing_user(): void
    {
        // Given
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password1'),
        ]);

        // When
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        // Then
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_rejects_invalid_credentials(): void
    {
        // Given
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password1'),
        ]);

        // When
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Then
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function it_logs_out_authenticated_user(): void
    {
        // Given
        $user = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->post(route('logout'));

        // Then
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function it_redirects_authenticated_user_from_login(): void
    {
        // Given
        $user = User::factory()->create();
        $this->actingAs($user);

        // When
        $response = $this->get(route('login'));

        // Then
        $response->assertRedirect('/');
    }
}
