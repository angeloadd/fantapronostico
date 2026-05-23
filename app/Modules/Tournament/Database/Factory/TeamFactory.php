<?php

declare(strict_types=1);

namespace App\Modules\Tournament\Database\Factory;

use App\Modules\Tournament\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
final class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->country,
            'api_id' => $this->faker->randomNumber(),
            'code' => $this->faker->countryISOAlpha3,
            'logo' => $this->faker->imageUrl(100, 100, 'sports'),
            'is_national' => $this->faker->boolean,
        ];
    }

    /**
     * @return Factory<Team>
     */
    public function national(): Factory
    {
        return $this->state(fn (): array => ['is_national' => true]);
    }

    /**
     * @return Factory<Team>
     */
    public function club(): Factory
    {
        return $this->state(fn (): array => ['is_national' => false]);
    }
}
