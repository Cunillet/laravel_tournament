<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RoundDefinitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_game(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);

        // Then
        $this->assertTrue($round->game->is($game));
    }

    /** @test */
    public function it_has_scoring_rules_relationship(): void
    {
        // Given
        $round = RoundDefinition::factory()->create();
        $rule  = ScoringRule::factory()->create(['round_id' => $round->id]);

        // When
        $result = $round->scoringRules;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($rule));
    }

    /** @test */
    public function it_casts_rounds_count_as_integer(): void
    {
        // Given
        $round = RoundDefinition::factory()->create(['rounds_count' => 3]);

        // Then
        $this->assertSame(3, $round->rounds_count);
        $this->assertIsInt($round->rounds_count);
    }
}
