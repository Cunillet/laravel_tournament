<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Tournament;
use App\Models\TournamentRound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TournamentRoundTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_detects_pending_status(): void
    {
        // Given
        $round = TournamentRound::factory()->create(['status' => 'pending']);

        // Then
        $this->assertTrue($round->isPending());
        $this->assertFalse($round->isActive());
        $this->assertFalse($round->isClosed());
    }

    /** @test */
    public function it_detects_active_status(): void
    {
        // Given
        $round = TournamentRound::factory()->create(['status' => 'active']);

        // Then
        $this->assertTrue($round->isActive());
        $this->assertFalse($round->isPending());
        $this->assertFalse($round->isClosed());
    }

    /** @test */
    public function it_detects_closed_status(): void
    {
        // Given
        $round = TournamentRound::factory()->create(['status' => 'closed']);

        // Then
        $this->assertTrue($round->isClosed());
        $this->assertFalse($round->isPending());
        $this->assertFalse($round->isActive());
    }

    /** @test */
    public function it_belongs_to_tournament(): void
    {
        // Given
        $tournament = Tournament::factory()->create();
        $round      = TournamentRound::factory()->create(['tournament_id' => $tournament->id]);

        // Then
        $this->assertTrue($round->tournament->is($tournament));
    }
}
