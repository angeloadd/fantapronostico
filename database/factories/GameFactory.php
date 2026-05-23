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
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'started_at' => $this->faker->unixTime,
            'stage' => $this->faker->word,
            'status' => 'not_started',
        ];
    }
}
