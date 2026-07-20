<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use App\Models\RoundDefinition;
use App\Models\ScoringRule;
use App\Models\ScoringSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScoringRule>
 */
final class ScoringRuleFactory extends Factory
{
    protected $model = ScoringRule::class;

    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'round_id' => null,
            'scoring_system_id' => ScoringSystem::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'min_score' => 0,
            'max_score' => 100,
            'priority' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }

    public function forRound(RoundDefinition $round): static
    {
        return $this->state(fn (array $attributes) => [
            'round_id' => $round->id,
        ]);
    }
}
