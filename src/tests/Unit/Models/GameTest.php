<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchRound;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GameTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_rounds_relationship(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0]);

        // When
        $result = $game->rounds;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($round));
    }

    /** @test */
    public function it_orders_rounds_by_order_field(): void
    {
        // Given
        $game   = Game::factory()->create();
        $round2 = RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 2]);
        $round0 = RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0]);
        $round1 = RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 1]);

        // When
        $rounds = $game->rounds;

        // Then
        $this->assertTrue($rounds[0]->is($round0));
        $this->assertTrue($rounds[1]->is($round1));
        $this->assertTrue($rounds[2]->is($round2));
    }

    /** @test */
    public function it_has_scoring_rules_relationship(): void
    {
        // Given
        $game = Game::factory()->create();
        $rule = ScoringRule::factory()->create(['game_id' => $game->id]);

        // When
        $result = $game->scoringRules;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($rule));
    }

    /** @test */
    public function it_has_game_matches_relationship(): void
    {
        // Given
        $game  = Game::factory()->create();
        $match = GameMatch::factory()->create(['game_id' => $game->id]);

        // When
        $result = $game->gameMatches;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    /** @test */
    public function it_has_match_rounds_relationship(): void
    {
        // Given
        $game       = Game::factory()->create();
        $gameMatch  = GameMatch::factory()->create(['game_id' => $game->id]);
        $matchRound = MatchRound::factory()->create(['game_match_id' => $gameMatch->id]);

        // When
        $result = $game->matchRounds;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($matchRound));
    }

    /** @test */
    public function it_has_tournaments_relationship(): void
    {
        // Given
        $game       = Game::factory()->create();
        $tournament = Tournament::factory()->create(['game_id' => $game->id]);

        // When
        $result = $game->tournaments;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($tournament));
    }
}
