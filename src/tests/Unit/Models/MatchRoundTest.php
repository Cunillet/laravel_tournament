<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\GameMatch;
use App\Models\MatchRound;
use App\Models\MatchScore;
use App\Models\RoundDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MatchRoundTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_game_match(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        $mr    = MatchRound::factory()->create(['game_match_id' => $match->id]);

        // Then
        $this->assertTrue($mr->gameMatch->is($match));
    }

    /** @test */
    public function it_belongs_to_round_definition(): void
    {
        // Given
        $rd = RoundDefinition::factory()->create();
        $mr = MatchRound::factory()->create(['round_id' => $rd->id]);

        // Then
        $this->assertTrue($mr->round->is($rd));
    }

    /** @test */
    public function it_has_scores_relationship(): void
    {
        // Given
        $mr    = MatchRound::factory()->create();
        $score = MatchScore::factory()->create(['match_round_id' => $mr->id]);

        // When
        $result = $mr->scores;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($score));
    }

    /** @test */
    public function it_casts_dates_as_datetime(): void
    {
        // Given
        $mr = MatchRound::factory()->completed()->create();

        // Then
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $mr->started_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $mr->completed_at);
    }
}
