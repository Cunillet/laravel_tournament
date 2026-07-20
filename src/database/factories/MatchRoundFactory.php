<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\MatchRound;
use App\Models\RoundDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchRound>
 */
final class MatchRoundFactory extends Factory
{
    protected $model = MatchRound::class;

    public function definition(): array
    {
        return [
            'game_match_id' => GameMatch::factory(),
            'round_id' => RoundDefinition::factory(),
            'status' => 'pending',
            'order' => fake()->numberBetween(0, 10),
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
    }
}
