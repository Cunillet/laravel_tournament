<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Models\ScoringSystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ScoringRuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_game(): void
    {
        // Given
        $game = Game::factory()->create();
        $rule = ScoringRule::factory()->create(['game_id' => $game->id]);

        // Then
        $this->assertTrue($rule->game->is($game));
    }

    /** @test */
    public function it_belongs_to_round(): void
    {
        // Given
        $round = RoundDefinition::factory()->create();
        $rule  = ScoringRule::factory()->create(['round_id' => $round->id]);

        // Then
        $this->assertTrue($rule->round->is($round));
    }

    /** @test */
    public function it_belongs_to_scoring_system(): void
    {
        // Given
        $system = ScoringSystem::factory()->create();
        $rule   = ScoringRule::factory()->create(['scoring_system_id' => $system->id]);

        // Then
        $this->assertTrue($rule->scoringSystem->is($system));
    }

    /** @test */
    public function it_casts_boolean_is_active(): void
    {
        // Given
        $rule = ScoringRule::factory()->create(['is_active' => true]);

        // Then
        $this->assertTrue($rule->is_active);
        $this->assertIsBool($rule->is_active);
    }
}
