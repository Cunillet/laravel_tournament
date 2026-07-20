<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Models\ScoringSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /** @test */
    public function admin_can_list_games(): void
    {
        // Given
        Game::factory()->count(3)->create();
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('games.index'));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Games/Index'));
    }

    /** @test */
    public function non_admin_cannot_list_games(): void
    {
        // Given
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $this->actingAs($user);

        // When
        $response = $this->get(route('games.index'));

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_a_game(): void
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->post(route('games.store'), [
            'name' => 'Test Game',
            'description' => 'A game description',
            'objectives' => 'Win the game',
        ]);

        // Then
        $response->assertRedirect(route('games.index'));
        $this->assertDatabaseHas('games', ['name' => 'Test Game']);
    }

    /** @test */
    public function admin_can_view_a_game(): void
    {
        // Given
        $game = Game::factory()->create();
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('games.show', $game));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Games/Show'));
    }

    /** @test */
    public function admin_can_update_a_game(): void
    {
        // Given
        $game = Game::factory()->create(['name' => 'Old Name']);
        $this->actingAs($this->admin);

        // When
        $response = $this->put(route('games.update', $game), [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'objectives' => 'Updated objectives',
        ]);

        // Then
        $response->assertRedirect(route('games.index'));
        $this->assertDatabaseHas('games', ['name' => 'Updated Name']);
    }

    /** @test */
    public function admin_can_delete_a_game(): void
    {
        // Given
        $game = Game::factory()->create();
        $this->actingAs($this->admin);

        // When
        $response = $this->delete(route('games.destroy', $game));

        // Then
        $response->assertRedirect(route('games.index'));
        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    /** @test */
    public function admin_can_add_round_definition(): void
    {
        // Given
        $game = Game::factory()->create();
        $this->actingAs($this->admin);

        // When
        $response = $this->post(route('games.round-definitions.store', $game), [
            'name' => 'Round 1',
            'description' => 'First round',
            'order' => 0,
            'rounds_count' => 1,
        ]);

        // Then
        $response->assertRedirect(route('games.edit', $game));
        $this->assertDatabaseHas('round_definitions', [
            'game_id' => $game->id,
            'name' => 'Round 1',
        ]);
    }

    /** @test */
    public function admin_can_update_round_definition(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id, 'name' => 'Old Round']);
        $this->actingAs($this->admin);

        // When
        $response = $this->put(route('games.round-definitions.update', [$game, $round]), [
            'name' => 'Updated Round',
            'description' => 'Updated',
            'order' => 0,
            'rounds_count' => 2,
        ]);

        // Then
        $response->assertRedirect(route('games.edit', $game));
        $this->assertDatabaseHas('round_definitions', ['id' => $round->id, 'name' => 'Updated Round']);
    }

    /** @test */
    public function admin_can_delete_round_definition(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);
        $this->actingAs($this->admin);

        // When
        $response = $this->delete(route('games.round-definitions.destroy', [$game, $round]));

        // Then
        $response->assertRedirect(route('games.edit', $game));
        $this->assertDatabaseMissing('round_definitions', ['id' => $round->id]);
    }

    /** @test */
    public function admin_can_add_scoring_rule(): void
    {
        // Given
        $game   = Game::factory()->create();
        $system = ScoringSystem::factory()->create();
        $this->actingAs($this->admin);

        // When
        $response = $this->post(route('games.scoring-rules.store', $game), [
            'round_id' => null,
            'scoring_system_id' => $system->id,
            'name' => 'Points',
            'description' => 'Score points',
            'min_score' => 0,
            'max_score' => 100,
            'priority' => 0,
            'is_active' => true,
        ]);

        // Then
        $response->assertRedirect(route('games.edit', $game));
        $this->assertDatabaseHas('scoring_rules', [
            'game_id' => $game->id,
            'name' => 'Points',
        ]);
    }

    /** @test */
    public function admin_can_update_scoring_rule(): void
    {
        // Given
        $game  = Game::factory()->create();
        $system = ScoringSystem::factory()->create();
        $rule  = ScoringRule::factory()->create(['game_id' => $game->id, 'scoring_system_id' => $system->id, 'name' => 'Old Rule']);
        $this->actingAs($this->admin);

        // When
        $response = $this->put(route('games.scoring-rules.update', [$game, $rule]), [
            'scoring_system_id' => $system->id,
            'name' => 'Updated Rule',
            'description' => 'Updated',
            'min_score' => null,
            'max_score' => null,
            'priority' => 1,
            'is_active' => true,
        ]);

        // Then
        $response->assertRedirect(route('games.edit', $game));
        $this->assertDatabaseHas('scoring_rules', ['id' => $rule->id, 'name' => 'Updated Rule']);
    }

    /** @test */
    public function admin_can_delete_scoring_rule(): void
    {
        // Given
        $game = Game::factory()->create();
        $rule = ScoringRule::factory()->create(['game_id' => $game->id]);
        $this->actingAs($this->admin);

        // When
        $response = $this->delete(route('games.scoring-rules.destroy', [$game, $rule]));

        // Then
        $response->assertRedirect(route('games.edit', $game));
        $this->assertDatabaseMissing('scoring_rules', ['id' => $rule->id]);
    }

    /** @test */
    public function it_validates_game_store_request(): void
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->post(route('games.store'), [
            'name' => '',
        ]);

        // Then
        $response->assertSessionHasErrors(['name']);
    }
}
