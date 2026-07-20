<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MatchPlayerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_game_match(): void
    {
        // Given
        $match  = GameMatch::factory()->create();
        $player = MatchPlayer::factory()->create(['game_match_id' => $match->id]);

        // Then
        $this->assertTrue($player->gameMatch->is($match));
    }

    /** @test */
    public function it_belongs_to_user(): void
    {
        // Given
        $user   = User::factory()->create();
        $player = MatchPlayer::factory()->create(['user_id' => $user->id]);

        // Then
        $this->assertTrue($player->user->is($user));
    }

    /** @test */
    public function it_has_scores_relationship(): void
    {
        // Given
        $player = MatchPlayer::factory()->create();
        $score  = MatchScore::factory()->create(['match_player_id' => $player->id]);

        // When
        $result = $player->scores;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($score));
    }

    /** @test */
    public function it_casts_finished_at_as_datetime(): void
    {
        // Given
        $player = MatchPlayer::factory()->finished()->create();

        // Then
        $this->assertNotNull($player->finished_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $player->finished_at);
    }
}
