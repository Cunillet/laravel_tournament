<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use App\Models\RoundDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoundDefinition>
 */
final class RoundDefinitionFactory extends Factory
{
    protected $model = RoundDefinition::class;

    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'order' => fake()->numberBetween(0, 10),
            'rounds_count' => 1,
        ];
    }
}
