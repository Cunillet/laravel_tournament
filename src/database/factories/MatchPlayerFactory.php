<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchPlayer>
 */
final class MatchPlayerFactory extends Factory
{
    protected $model = MatchPlayer::class;

    public function definition(): array
    {
        return [
            'game_match_id' => GameMatch::factory(),
            'user_id' => User::factory(),
            'finished_at' => null,
        ];
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'finished_at' => now(),
        ]);
    }
}
