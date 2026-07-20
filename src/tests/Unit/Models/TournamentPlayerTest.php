<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TournamentPlayerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create();
        $player     = TournamentPlayer::factory()->create(['tournament_id' => $tournament->id]);

        // Then
        $this->assertTrue($player->tournament->is($tournament));
    }

    /** @test */
    public function it_belongs_to_user(): void
    {
        // Given
        $user   = User::factory()->create();
        $player = TournamentPlayer::factory()->create(['user_id' => $user->id]);

        // Then
        $this->assertTrue($player->user->is($user));
    }
}
