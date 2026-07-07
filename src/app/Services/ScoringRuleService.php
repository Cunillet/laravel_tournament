<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ScoringRule;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for querying ScoringRule models.
 *
 * Encapsulates the logic for fetching scoring rules by game and round,
 * including global rules (round_id = null).
 */
final class ScoringRuleService
{
    /**
     * Get scoring rules for a specific game and round.
     *
     * Returns rules explicitly assigned to the round, plus any global rules
     * (round_id = null) that apply to all rounds of the game.
     *
     * @param int $gameId  The game ID.
     * @param int $roundId The round definition ID.
     * @return Collection<int, ScoringRule>
     */
    public function getByGameAndRound(int $gameId, int $roundId): Collection
    {
        return ScoringRule::query()
            ->where('game_id', $gameId)
            ->where(function ($query) use ($roundId): void {
                $query->where('round_id', $roundId)
                    ->orWhereNull('round_id');
            })
            ->orderBy('priority')
            ->get();
    }

    /**
     * Determine whether a scoring rule belongs to a given game and round.
     *
     * Checks both round-specific rules and global rules (round_id = null).
     *
     * @param int $gameId  The game ID.
     * @param int $roundId The round definition ID.
     * @param int $ruleId  The scoring rule ID to check.
     * @return bool
     */
    public function existsForGameAndRound(int $gameId, int $roundId, int $ruleId): bool
    {
        return ScoringRule::query()
            ->where('id', $ruleId)
            ->where('game_id', $gameId)
            ->where(function ($query) use ($roundId): void {
                $query->where('round_id', $roundId)
                    ->orWhereNull('round_id');
            })
            ->exists();
    }
}
