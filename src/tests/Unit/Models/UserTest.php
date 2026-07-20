<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MatchPlayer;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_role_constants(): void
    {
        $this->assertSame(0, User::ROLE_ADMIN);
        $this->assertSame(1, User::ROLE_MANAGER);
        $this->assertSame(2, User::ROLE_USER);
    }

    /** @test */
    public function it_detects_admin_role(): void
    {
        // Given
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user  = User::factory()->create(['role' => User::ROLE_USER]);

        // Then
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_detects_manager_role(): void
    {
        // Given
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $user    = User::factory()->create(['role' => User::ROLE_USER]);

        // Then
        $this->assertTrue($manager->isManager());
        $this->assertFalse($user->isManager());
    }

    /** @test */
    public function it_has_match_players_relationship(): void
    {
        // Given
        $user   = User::factory()->create();
        $player = MatchPlayer::factory()->create(['user_id' => $user->id]);

        // When
        $result = $user->matchPlayers;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($player));
    }

    /** @test */
    public function it_has_tournament_entries_relationship(): void
    {
        // Given
        $user  = User::factory()->create();
        $entry = TournamentPlayer::factory()->create(['user_id' => $user->id]);

        // When
        $result = $user->tournamentEntries;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($entry));
    }

    /** @test */
    public function it_has_managed_tournaments_relationship(): void
    {
        // Given
        $user       = User::factory()->create();
        $tournament = Tournament::factory()->create(['created_by' => $user->id]);

        // When
        $result = $user->managedTournaments;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($tournament));
    }
}
