<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\TournamentMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GameMatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_game(): void
    {
        // Given
        $game  = Game::factory()->create();
        $match = GameMatch::factory()->create(['game_id' => $game->id]);

        // Then
        $this->assertTrue($match->game->is($game));
    }

    /** @test */
    public function it_has_players_relationship(): void
    {
        // Given
        $match  = GameMatch::factory()->create();
        $player = MatchPlayer::factory()->create(['game_match_id' => $match->id]);

        // When
        $result = $match->players;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($player));
    }

    /** @test */
    public function it_has_rounds_relationship_ordered(): void
    {
        // Given
        $match  = GameMatch::factory()->create();
        $round2 = MatchRound::factory()->create(['game_match_id' => $match->id, 'order' => 2]);
        $round0 = MatchRound::factory()->create(['game_match_id' => $match->id, 'order' => 0]);

        // When
        $rounds = $match->rounds;

        // Then
        $this->assertCount(2, $rounds);
        $this->assertTrue($rounds[0]->is($round0));
        $this->assertTrue($rounds[1]->is($round2));
    }

    /** @test */
    public function it_has_tournament_match_relationship(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        $tm    = TournamentMatch::factory()->create(['game_match_id' => $match->id]);

        // Then
        $this->assertTrue($match->tournamentMatch->is($tm));
    }
}
