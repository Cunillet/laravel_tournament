<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\MatchScore;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Models\ScoringSystem;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentPlayer;
use App\Models\TournamentRound;
use App\Models\User;
use App\Services\StandingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StandingsServiceTest extends TestCase
{
    use RefreshDatabase;

    private StandingsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StandingsService::class);
    }

    /** @test */
    public function it_returns_null_when_no_completed_matches(): void
    {
        // Given
        $tournament = Tournament::factory()->create();

        // When
        $result = $this->service->buildStandings($tournament);

        // Then
        $this->assertNull($result);
    }

    /** @test */
    public function it_builds_standings_with_scores(): void
    {
        // Given
        $game          = Game::factory()->create();
        $roundDef      = RoundDefinition::factory()->create(['game_id' => $game->id, 'order' => 0]);
        $scoringSystem = ScoringSystem::factory()->create();
        $scoringRule   = ScoringRule::factory()->create([
            'game_id' => $game->id,
            'round_id' => $roundDef->id,
            'scoring_system_id' => $scoringSystem->id,
            'priority' => 0,
        ]);

        $tournament      = Tournament::factory()->create(['game_id' => $game->id]);
        $player1         = User::factory()->create(['nickname' => 'Player1']);
        $player2         = User::factory()->create(['nickname' => 'Player2']);

        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $player1->id]);
        TournamentPlayer::factory()->create(['tournament_id' => $tournament->id, 'user_id' => $player2->id]);

        $gameMatch = GameMatch::factory()->completed()->create(['game_id' => $game->id]);
        $mp1       = MatchPlayer::factory()->finished()->create(['game_match_id' => $gameMatch->id, 'user_id' => $player1->id]);
        $mp2       = MatchPlayer::factory()->finished()->create(['game_match_id' => $gameMatch->id, 'user_id' => $player2->id]);

        $matchRound = MatchRound::factory()->completed()->create([
            'game_match_id' => $gameMatch->id,
            'round_id' => $roundDef->id,
        ]);

        MatchScore::factory()->create(['match_round_id' => $matchRound->id, 'match_player_id' => $mp1->id, 'scoring_rule_id' => $scoringRule->id, 'score' => 100]);
        MatchScore::factory()->create(['match_round_id' => $matchRound->id, 'match_player_id' => $mp2->id, 'scoring_rule_id' => $scoringRule->id, 'score' => 50]);

        $tournamentRound = TournamentRound::factory()->create(['tournament_id' => $tournament->id, 'round_number' => 1]);
        TournamentMatch::factory()->create(['tournament_round_id' => $tournamentRound->id, 'game_match_id' => $gameMatch->id]);

        // When
        $result = $this->service->buildStandings($tournament);

        // Then
        $this->assertNotNull($result);
        $this->assertCount(1, $result['rules']);
        $this->assertCount(2, $result['players']);

        // Player1 should be first (higher score)
        $this->assertSame('Player1', $result['players'][0]['user']['nickname']);
        $this->assertSame(100.0, $result['players'][0]['scores'][$scoringRule->id]);

        $this->assertSame('Player2', $result['players'][1]['user']['nickname']);
        $this->assertSame(50.0, $result['players'][1]['scores'][$scoringRule->id]);
    }
}
