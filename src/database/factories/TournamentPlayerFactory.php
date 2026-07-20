<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TournamentPlayer>
 */
final class TournamentPlayerFactory extends Factory
{
    protected $model = TournamentPlayer::class;

    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'user_id' => User::factory(),
        ];
    }
}
