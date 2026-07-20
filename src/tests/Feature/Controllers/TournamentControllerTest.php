<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringSystem;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TournamentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $manager;
    private User $admin;
    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user    = User::factory()->create(['role' => User::ROLE_USER]);
        $this->manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->admin   = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->game    = Game::factory()->create();
    }

    /** @test */
    public function it_lists_tournaments(): void
    {
        // Given
        Tournament::factory()->create(['game_id' => $this->game->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->get(route('tournaments.index'));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tournaments/Index'));
    }

    /** @test */
    public function it_shows_create_form_for_manager(): void
    {
        // Given
        $this->actingAs($this->manager);

        // When
        $response = $this->get(route('tournaments.create'));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tournaments/Create'));
    }

    /** @test */
    public function it_blocks_non_manager_from_create(): void
    {
        // Given
        $this->actingAs($this->user);

        // When
        $response = $this->get(route('tournaments.create'));

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_create_tournament(): void
    {
        // Given
        $this->actingAs($this->manager);

        // When
        $response = $this->post(route('tournaments.store'), [
            'name' => 'Test Tournament',
            'description' => 'A tournament',
            'game_id' => $this->game->id,
        ]);

        // Then
        $response->assertRedirect(route('tournaments.index'));
        $this->assertDatabaseHas('tournaments', [
            'name' => 'Test Tournament',
            'created_by' => $this->manager->id,
        ]);
    }

    /** @test */
    public function it_blocks_user_from_creating_tournament(): void
    {
        // Given
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('tournaments.store'), [
            'name' => 'Test Tournament',
            'game_id' => $this->game->id,
        ]);

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function it_shows_tournament_details(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->get(route('tournaments.show', $tournament));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tournaments/Show'));
    }

    /** @test */
    public function user_can_join_a_pending_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'pending']);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('tournaments.join', $tournament));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('tournament_players', [
            'tournament_id' => $tournament->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_cannot_join_already_joined_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'pending']);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('tournaments.join', $tournament));

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Ya estás inscrito en este torneo.');
    }

    /** @test */
    public function user_cannot_join_active_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'active']);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('tournaments.join', $tournament));

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_leave_a_pending_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'pending']);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('tournaments.leave', $tournament));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseMissing('tournament_players', [
            'tournament_id' => $tournament->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_cannot_leave_active_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'active']);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('tournaments.leave', $tournament));

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('error', 'No puedes salir de un torneo que ya ha comenzado.');
    }

    /** @test */
    public function manager_can_start_a_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'pending']);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->user->id]);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->manager->id]);
        $this->actingAs($this->manager);

        // When
        $response = $this->post(route('tournaments.start', $tournament));

        // Then
        $response->assertRedirect();
        $this->assertSame('active', $tournament->fresh()->status);
    }

    /** @test */
    public function it_requires_min_two_players_to_start(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'pending']);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->manager);

        // When
        $response = $this->post(route('tournaments.start', $tournament));

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Se necesitan al menos 2 jugadores para iniciar.');
    }

    /** @test */
    public function manager_can_create_rounds(): void
    {
        // Given
        RoundDefinition::factory()->create(['game_id' => $this->game->id, 'order' => 0, 'rounds_count' => 1]);
        $tournament = Tournament::factory()->active()->create(['game_id' => $this->game->id]);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->user->id]);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $this->manager->id]);
        $this->actingAs($this->manager);

        // When
        $response = $this->post(route('tournaments.rounds.store', $tournament));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('tournament_rounds', [
            'tournament_id' => $tournament->id,
            'round_number' => 1,
        ]);
    }

    /** @test */
    public function manager_can_close_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id, 'status' => 'active']);
        $this->actingAs($this->manager);

        // When
        $response = $this->post(route('tournaments.close', $tournament));

        // Then
        $response->assertRedirect();
        $this->assertSame('closed', $tournament->fresh()->status);
    }

    /** @test */
    public function admin_can_destroy_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id]);
        $this->actingAs($this->admin);

        // When
        $response = $this->delete(route('tournaments.destroy', $tournament));

        // Then
        $response->assertRedirect(route('tournaments.index'));
        $this->assertDatabaseMissing('tournaments', ['id' => $tournament->id]);
    }

    /** @test */
    public function non_admin_cannot_destroy_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['game_id' => $this->game->id]);
        $this->actingAs($this->manager);

        // When
        $response = $this->delete(route('tournaments.destroy', $tournament));

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_tournament_store_request(): void
    {
        // Given
        $this->actingAs($this->manager);

        // When
        $response = $this->post(route('tournaments.store'), [
            'name' => '',
            'game_id' => 9999,
        ]);

        // Then
        $response->assertSessionHasErrors(['name', 'game_id']);
    }
}
