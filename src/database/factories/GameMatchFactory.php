<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameMatch>
 */
final class GameMatchFactory extends Factory
{
    protected $model = GameMatch::class;

    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'status' => 'pending',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
