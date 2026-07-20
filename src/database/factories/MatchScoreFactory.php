<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\MatchScore;
use App\Models\ScoringRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchScore>
 */
final class MatchScoreFactory extends Factory
{
    protected $model = MatchScore::class;

    public function definition(): array
    {
        return [
            'match_round_id' => MatchRound::factory(),
            'match_player_id' => MatchPlayer::factory(),
            'scoring_rule_id' => ScoringRule::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'notes' => null,
        ];
    }
}
