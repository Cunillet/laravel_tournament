<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ScoringSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScoringSystem>
 */
final class ScoringSystemFactory extends Factory
{
    protected $model = ScoringSystem::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
        ];
    }
}
