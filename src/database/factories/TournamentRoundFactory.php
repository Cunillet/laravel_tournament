<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\TournamentRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TournamentRound>
 */
final class TournamentRoundFactory extends Factory
{
    protected $model = TournamentRound::class;

    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'round_number' => 1,
            'status' => 'pending',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }
}
