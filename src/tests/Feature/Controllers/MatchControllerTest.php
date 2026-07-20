<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\MatchScore;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Models\ScoringSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MatchControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $player2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user    = User::factory()->create();
        $this->player2 = User::factory()->create();
    }

    /** @test */
    public function it_lists_matches_for_current_user(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->get(route('matches.index'));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Matches/Index'));
    }

    /** @test */
    public function it_shows_match_details_for_player(): void
    {
        // Given
        $game  = Game::factory()->create();
        $roundDef = RoundDefinition::factory()->create(['game_id' => $game->id]);
        $match = GameMatch::factory()->create(['game_id' => $game->id]);

        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->get(route('matches.show', $match));

        // Then
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Matches/Show'));
    }

    /** @test */
    public function it_blocks_non_player_from_match_details(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->player2->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->get(route('matches.show', $match));

        // Then
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_any_match(): void
    {
        // Given
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $match = GameMatch::factory()->create();
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->player2->id]);
        $this->actingAs($admin);

        // When
        $response = $this->get(route('matches.show', $match));

        // Then
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_score_for_a_player(): void
    {
        // Given
        $game      = Game::factory()->create();
        $system    = ScoringSystem::factory()->create();
        $roundDef  = RoundDefinition::factory()->create(['game_id' => $game->id]);
        $scoringRule = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $roundDef->id,
            'scoring_system_id' => $system->id,
        ]);
        $match     = GameMatch::factory()->create(['game_id' => $game->id]);
        $matchPlayer = MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $matchRound  = MatchRound::factory()->create(['game_match_id' => $match->id, 'round_id' => $roundDef->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('matches.rounds.scores.upsert', $matchRound), [
            'match_player_id' => $matchPlayer->id,
            'scoring_rule_id' => $scoringRule->id,
            'score' => 85.5,
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('match_scores', [
            'match_round_id' => $matchRound->id,
            'match_player_id' => $matchPlayer->id,
            'scoring_rule_id' => $scoringRule->id,
            'score' => 85.5,
        ]);
    }

    /** @test */
    public function it_rejects_score_update_for_closed_match(): void
    {
        // Given
        $game      = Game::factory()->create();
        $roundDef  = RoundDefinition::factory()->create(['game_id' => $game->id]);
        $system    = ScoringSystem::factory()->create();
        $scoringRule = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $roundDef->id,
            'scoring_system_id' => $system->id,
        ]);
        $match     = GameMatch::factory()->completed()->create(['game_id' => $game->id]);
        $matchPlayer = MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $matchRound  = MatchRound::factory()->create(['game_match_id' => $match->id, 'round_id' => $roundDef->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('matches.rounds.scores.upsert', $matchRound), [
            'match_player_id' => $matchPlayer->id,
            'scoring_rule_id' => $scoringRule->id,
            'score' => 85.5,
        ]);

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('error', 'La partida está cerrada.');
    }

    /** @test */
    public function player_can_finish_match(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('matches.player-finish', $match));

        // Then
        $response->assertRedirect();
        $this->assertNotNull(MatchPlayer::where('game_match_id', $match->id)
            ->where('user_id', $this->user->id)
            ->first()->finished_at);
    }

    /** @test */
    public function match_completes_when_all_players_finish(): void
    {
        // Given
        $match = GameMatch::factory()->create(['status' => 'pending']);
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->player2->id]);
        $this->actingAs($this->user);

        // When - first player finishes
        $this->post(route('matches.player-finish', $match));

        // Then - match should still be pending
        $this->assertSame('pending', $match->fresh()->status);
    }

    /** @test */
    public function match_closes_when_all_finished(): void
    {
        // Given
        $match = GameMatch::factory()->create(['status' => 'pending']);
        MatchPlayer::factory()->finished()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->player2->id]);
        $this->actingAs($this->player2);

        // When - last player finishes
        $this->post(route('matches.player-finish', $match));

        // Then
        $this->assertSame('completed', $match->fresh()->status);
    }

    /** @test */
    public function it_can_force_close_a_match(): void
    {
        // Given
        $match = GameMatch::factory()->create(['status' => 'pending']);
        MatchPlayer::factory()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('matches.close', $match));

        // Then
        $response->assertRedirect();
        $this->assertSame('completed', $match->fresh()->status);
    }

    /** @test */
    public function it_rejects_closing_an_already_closed_match(): void
    {
        // Given
        $match = GameMatch::factory()->completed()->create();
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('matches.close', $match));

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('error', 'La partida ya está cerrada.');
    }

    /** @test */
    public function it_rejects_player_finishing_twice(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        MatchPlayer::factory()->finished()->create(['game_match_id' => $match->id, 'user_id' => $this->user->id]);
        $this->actingAs($this->user);

        // When
        $response = $this->post(route('matches.player-finish', $match));

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('info', 'Ya has finalizado tu puntuación.');
    }
}
