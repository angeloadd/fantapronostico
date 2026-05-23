<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Player>
 */
final class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstNameMale;
        $lastName = $this->faker->lastName;
        $displayedName = ucfirst($firstName[0]).'. '.$lastName;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'displayed_name' => $displayedName,
        ];
    }
}
