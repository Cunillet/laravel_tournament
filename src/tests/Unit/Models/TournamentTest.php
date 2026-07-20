<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\TournamentRound;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TournamentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_detects_pending_status(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['status' => 'pending']);

        // Then
        $this->assertTrue($tournament->isPending());
        $this->assertFalse($tournament->isActive());
        $this->assertFalse($tournament->isClosed());
    }

    /** @test */
    public function it_detects_active_status(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['status' => 'active']);

        // Then
        $this->assertTrue($tournament->isActive());
        $this->assertFalse($tournament->isPending());
        $this->assertFalse($tournament->isClosed());
    }

    /** @test */
    public function it_detects_closed_status(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['status' => 'closed']);

        // Then
        $this->assertTrue($tournament->isClosed());
        $this->assertFalse($tournament->isPending());
        $this->assertFalse($tournament->isActive());
    }

    /** @test */
    public function it_belongs_to_game(): void
    {
        // Given
        $game       = Game::factory()->create();
        $tournament = Tournament::factory()->create(['game_id' => $game->id]);

        // Then
        $this->assertTrue($tournament->game->is($game));
    }

    /** @test */
    public function it_belongs_to_creator(): void
    {
        // Given
        $creator    = User::factory()->create();
        $tournament = Tournament::factory()->create(['created_by' => $creator->id]);

        // Then
        $this->assertTrue($tournament->creator->is($creator));
    }

    /** @test */
    public function it_has_players_relationship(): void
    {
        // Given
        $tournament = Tournament::factory()->create();
        $player     = TournamentPlayer::factory()->create(['tournament_id' => $tournament->id]);

        // When
        $result = $tournament->players;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($player));
    }

    /** @test */
    public function it_has_rounds_relationship_sorted_by_round_number(): void
    {
        // Given
        $tournament = Tournament::factory()->create();
        $round2     = TournamentRound::factory()->create(['tournament_id' => $tournament->id, 'round_number' => 2]);
        $round1     = TournamentRound::factory()->create(['tournament_id' => $tournament->id, 'round_number' => 1]);

        // When
        $rounds = $tournament->rounds;

        // Then
        $this->assertCount(2, $rounds);
        $this->assertTrue($rounds[0]->is($round1));
        $this->assertTrue($rounds[1]->is($round2));
    }

    /** @test */
    public function it_can_be_purged(): void
    {
        // Given
        $tournament = Tournament::factory()->create();
        $id         = $tournament->id;

        // When
        $tournament->purge();

        // Then
        $this->assertNull(Tournament::find($id));
    }
}
