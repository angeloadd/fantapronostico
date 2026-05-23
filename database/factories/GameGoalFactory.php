<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GameGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameGoal>
 */
final class GameGoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scored_at' => $this->faker->numberBetween(1, 128),
        ];
    }
}
