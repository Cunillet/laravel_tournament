<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Support\Collection;

/**
 * Service for computing tournament standings (leaderboard).
 *
 * Aggregates scores from all completed matches per player per scoring rule,
 * and sorts players lexicographically by rule priority.
 */
final class StandingsService
{
    public function __construct(
        private readonly ScoringRuleService $scoringRuleService,
    ) {}

    /**
     * Build the standings structure for a tournament.
     *
     * Returns null when there are no completed matches or no scoring rules.
     *
     * @return array{rules: array, players: array}|null
     */
    public function buildStandings(Tournament $tournament): ?array
    {
        $matches = $this->getCompletedMatches($tournament);

        if ($matches->isEmpty()) {
            return null;
        }

        $sortedRules = $this->collectSortedRules($matches);

        if (empty($sortedRules)) {
            return null;
        }

        $players = $this->accumulateScores($matches, $sortedRules);

        if (empty($players)) {
            return null;
        }

        $players = $this->sortPlayersByPriority($players, $sortedRules);

        return [
            'rules' => $sortedRules,
            'players' => $players,
        ];
    }

    /**
     * Get all completed GameMatch instances for a tournament.
     *
     * Uses Eloquent through Tournament → TournamentRound → TournamentMatch → GameMatch.
     *
     * @return Collection<int, \App\Models\GameMatch>
     */
    private function getCompletedMatches(Tournament $tournament): Collection
    {
        return TournamentMatch::query()
            ->whereHas('tournamentRound', fn ($query) => $query->where('tournament_id', $tournament->id))
            ->with([
                'gameMatch' => function ($query): void {
                    $query->where('status', 'completed')->with([
                        'players.user:id,nickname',
                        'players.scores',
                        'rounds',
                        'rounds.scores',
                        'rounds.round',
                    ]);
                },
            ])
            ->get()
            ->pluck('gameMatch')
            ->filter()
            ->values();
    }

    /**
     * Collect unique scoring rules from all match rounds, sorted by priority.
     *
     * @param Collection<int, \App\Models\GameMatch> $matches
     * @return array<int, array{id: int, name: string, priority: int}>
     */
    private function collectSortedRules(Collection $matches): array
    {
        $rulesMap = [];

        foreach ($matches as $gm) {
            foreach ($gm->rounds as $mr) {
                if ($mr->round === null) {
                    continue;
                }

                $scoringRules = $this->scoringRuleService
                    ->getByGameAndRound($gm->game_id, $mr->round_id);

                foreach ($scoringRules as $rule) {
                    $rulesMap[$rule->id] = [
                        'id' => $rule->id,
                        'name' => $rule->name,
                        'priority' => $rule->priority ?? 0,
                    ];
                }
            }
        }

        if (empty($rulesMap)) {
            return [];
        }

        return collect($rulesMap)
            ->sortBy('priority')
            ->values()
            ->all();
    }

    /**
     * Accumulate scores per player per scoring rule across all matches.
     *
     * @param Collection<int, \App\Models\GameMatch> $matches
     * @param array<int, array{id: int, name: string, priority: int}> $sortedRules
     * @return array<int, array{user: array{id: int, nickname: string}, scores: array<int, float>}>
     */
    private function accumulateScores(Collection $matches, array $sortedRules): array
    {
        $playersMap = [];

        foreach ($matches as $gm) {
            foreach ($gm->rounds as $mr) {
                foreach ($mr->scores as $score) {
                    $matchPlayer = $gm->players->firstWhere('id', $score->match_player_id);

                    if ($matchPlayer === null || $matchPlayer->user === null) {
                        continue;
                    }

                    $userId = $matchPlayer->user_id;

                    if (!isset($playersMap[$userId])) {
                        $playersMap[$userId] = [
                            'user' => [
                                'id' => $matchPlayer->user->id,
                                'nickname' => $matchPlayer->user->nickname,
                            ],
                            'scores' => collect($sortedRules)
                                ->mapWithKeys(fn (array $rule) => [$rule['id'] => 0.0])
                                ->all(),
                        ];
                    }

                    $playersMap[$userId]['scores'][$score->scoring_rule_id] =
                        ($playersMap[$userId]['scores'][$score->scoring_rule_id] ?? 0.0) + (float) $score->score;
                }
            }
        }

        return $playersMap;
    }

    /**
     * Sort players lexicographically by scoring rule priority.
     *
     * Players are compared rule by rule in priority order. The player with the
     * higher score in the highest-priority rule ranks first. Ties are broken
     * by the next rule.
     *
     * @param array<int, array{user: array, scores: array}> $players
     * @param array<int, array{id: int, name: string, priority: int}> $sortedRules
     * @return array<int, array{user: array, scores: array}>
     */
    private function sortPlayersByPriority(array $players, array $sortedRules): array
    {
        $sorted = collect($players)->sort(function (array $a, array $b) use ($sortedRules): int {
            foreach ($sortedRules as $rule) {
                $scoreA = $a['scores'][$rule['id']] ?? 0.0;
                $scoreB = $b['scores'][$rule['id']] ?? 0.0;

                if ($scoreA !== $scoreB) {
                    return $scoreB <=> $scoreA;
                }
            }

            return 0;
        });

        return $sorted->values()->all();
    }
}
