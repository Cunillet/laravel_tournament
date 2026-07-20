<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
final class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'objectives' => fake()->paragraph(),
        ];
    }
}
