<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Services\ScoringRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ScoringRuleServiceTest extends TestCase
{
    use RefreshDatabase;

    private ScoringRuleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ScoringRuleService::class);
    }

    /** @test */
    public function it_gets_rules_by_game_and_round(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);

        $roundRule = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $round->id,
        ]);
        $globalRule = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => null,
        ]);
        // Rule from another game (should NOT appear)
        ScoringRule::factory()->create();

        // When
        $rules = $this->service->getByGameAndRound($game->id, $round->id);

        // Then
        $this->assertCount(2, $rules);
        $this->assertTrue($rules->pluck('id')->contains($roundRule->id));
        $this->assertTrue($rules->pluck('id')->contains($globalRule->id));
    }

    /** @test */
    public function it_returns_rules_ordered_by_priority(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);

        ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $round->id,
            'priority' => 2,
            'name' => 'low',
        ]);
        ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $round->id,
            'priority' => 0,
            'name' => 'high',
        ]);

        // When
        $rules = $this->service->getByGameAndRound($game->id, $round->id);

        // Then
        $this->assertSame('high', $rules[0]->name);
        $this->assertSame('low', $rules[1]->name);
    }

    /** @test */
    public function it_checks_if_rule_exists_for_game_and_round(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);

        $rule = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $round->id,
        ]);

        // Then
        $this->assertTrue($this->service->existsForGameAndRound($game->id, $round->id, $rule->id));
    }

    /** @test */
    public function it_checks_global_rule_exists_for_round(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);

        $rule = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => null,
        ]);

        // Then
        $this->assertTrue($this->service->existsForGameAndRound($game->id, $round->id, $rule->id));
    }

    /** @test */
    public function it_returns_false_when_rule_does_not_belong(): void
    {
        // Given
        $game  = Game::factory()->create();
        $round = RoundDefinition::factory()->create(['game_id' => $game->id]);
        $otherRule = ScoringRule::factory()->create();

        // Then
        $this->assertFalse($this->service->existsForGameAndRound($game->id, $round->id, $otherRule->id));
    }
}
