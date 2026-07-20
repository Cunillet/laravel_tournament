<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\GameMatch;
use App\Models\TournamentMatch;
use App\Models\TournamentRound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TournamentMatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_tournament_round(): void
    {
        // Given
        $round = TournamentRound::factory()->create();
        $tm    = TournamentMatch::factory()->create(['tournament_round_id' => $round->id]);

        // Then
        $this->assertTrue($tm->tournamentRound->is($round));
    }

    /** @test */
    public function it_belongs_to_game_match(): void
    {
        // Given
        $match = GameMatch::factory()->create();
        $tm    = TournamentMatch::factory()->create(['game_match_id' => $match->id]);

        // Then
        $this->assertTrue($tm->gameMatch->is($match));
    }
}
