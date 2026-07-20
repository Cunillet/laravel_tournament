<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\MatchScore;
use App\Models\ScoringRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MatchScoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_match_round(): void
    {
        // Given
        $mr    = MatchRound::factory()->create();
        $score = MatchScore::factory()->create(['match_round_id' => $mr->id]);

        // Then
        $this->assertTrue($score->matchRound->is($mr));
    }

    /** @test */
    public function it_belongs_to_match_player(): void
    {
        // Given
        $player = MatchPlayer::factory()->create();
        $score  = MatchScore::factory()->create(['match_player_id' => $player->id]);

        // Then
        $this->assertTrue($score->matchPlayer->is($player));
    }

    /** @test */
    public function it_belongs_to_scoring_rule(): void
    {
        // Given
        $rule  = ScoringRule::factory()->create();
        $score = MatchScore::factory()->create(['scoring_rule_id' => $rule->id]);

        // Then
        $this->assertTrue($score->scoringRule->is($rule));
    }

    /** @test */
    public function it_casts_score_as_decimal(): void
    {
        // Given
        $score = MatchScore::factory()->create(['score' => 75.50]);

        // Then
        $this->assertSame(75.50, (float) $score->score);
    }
}
