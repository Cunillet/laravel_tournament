<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringSystem;
use App\Models\Tournament;
use App\Models\User;
use App\Services\TournamentMatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TournamentMatchServiceTest extends TestCase
{
    use RefreshDatabase;

    private TournamentMatchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TournamentMatchService::class);
    }

    /** @test */
    public function it_creates_a_round_with_pairings(): void
    {
        // Given
        $game = Game::factory()->create();
        RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0, 'rounds_count' => 1]);

        $tournament = Tournament::factory()->active()->create(['game_id' => $game->id]);
        $player1    = User::factory()->create();
        $player2    = User::factory()->create();
        $tournament->players()->createMany([
            ['user_id' => $player1->id],
            ['user_id' => $player2->id],
        ]);

        // When
        $round = $this->service->createRound($tournament);

        // Then
        $this->assertSame(1, $round->round_number);
        $this->assertSame('active', $round->status);
        $this->assertCount(1, $round->matches);

        $gameMatch = $round->matches->first()->gameMatch;
        $this->assertNotNull($gameMatch);
        $this->assertCount(2, $gameMatch->players);
        $this->assertCount(1, $gameMatch->rounds);
    }

    /** @test */
    public function it_throws_when_tournament_not_active(): void
    {
        // Given
        $tournament = Tournament::factory()->create(['status' => 'pending']);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('El torneo no está activo.');

        // When
        $this->service->createRound($tournament);
    }

    /** @test */
    public function it_throws_when_fewer_than_two_players(): void
    {
        // Given
        $game       = Game::factory()->create();
        $tournament = Tournament::factory()->active()->create(['game_id' => $game->id]);
        $player1    = User::factory()->create();
        $tournament->players()->create(['user_id' => $player1->id]);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Se necesitan al menos 2 jugadores.');

        // When
        $this->service->createRound($tournament);
    }

    /** @test */
    public function it_generates_different_pairs_in_subsequent_rounds(): void
    {
        // Given
        $game = Game::factory()->create();
        RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0, 'rounds_count' => 1]);

        $tournament = Tournament::factory()->active()->create(['game_id' => $game->id]);
        $players    = User::factory()->count(4)->create();
        foreach ($players as $player) {
            $tournament->players()->create(['user_id' => $player->id]);
        }

        // When - create first round
        $round1 = $this->service->createRound($tournament);
        $firstPairs = $round1->matches->map(fn ($tm) => $tm->gameMatch->players->pluck('user_id')->sort()->values())->toArray();

        // When - create second round
        $round2 = $this->service->createRound($tournament);
        $secondPairs = $round2->matches->map(fn ($tm) => $tm->gameMatch->players->pluck('user_id')->sort()->values())->toArray();

        // Then - pairs should be different
        $this->assertNotEquals($firstPairs, $secondPairs);
    }

    /** @test */
    public function it_throws_when_no_more_pairs_available(): void
    {
        // Given
        $game = Game::factory()->create();
        RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0, 'rounds_count' => 1]);

        $tournament = Tournament::factory()->active()->create(['game_id' => $game->id]);
        $players    = User::factory()->count(2)->create();
        foreach ($players as $player) {
            $tournament->players()->create(['user_id' => $player->id]);
        }

        // When - create first round (the only possible pair)
        $this->service->createRound($tournament);

        // Then - second round should fail
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No hay más combinaciones posibles de emparejamientos.');

        $this->service->createRound($tournament);
    }

    /** @test */
    public function it_closes_a_round(): void
    {
        // Given
        $game = Game::factory()->create();
        RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0, 'rounds_count' => 1]);

        $tournament = Tournament::factory()->active()->create(['game_id' => $game->id]);
        $players    = User::factory()->count(4)->create();
        foreach ($players as $player) {
            $tournament->players()->create(['user_id' => $player->id]);
        }
        $round = $this->service->createRound($tournament);

        // When
        $this->service->closeRound($round);

        // Then
        $this->assertSame('closed', $round->fresh()->status);
        $this->assertFalse($tournament->fresh()->isClosed());
    }

    /** @test */
    public function it_closes_tournament_when_no_more_pairs_after_round_close(): void
    {
        // Given
        $game = Game::factory()->create();
        RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0, 'rounds_count' => 1]);

        $tournament = Tournament::factory()->active()->create(['game_id' => $game->id]);
        $players    = User::factory()->count(2)->create();
        foreach ($players as $player) {
            $tournament->players()->create(['user_id' => $player->id]);
        }
        $round = $this->service->createRound($tournament);

        // When
        $this->service->closeRound($round);

        // Then
        $this->assertTrue($tournament->fresh()->isClosed());
    }

    /** @test */
    public function it_closes_tournament_directly(): void
    {
        // Given
        $tournament = Tournament::factory()->active()->create();

        // When
        $this->service->closeTournament($tournament);

        // Then
        $this->assertTrue($tournament->fresh()->isClosed());
    }
}
