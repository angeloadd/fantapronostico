<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tournament;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tournament>
 */
final class TournamentFactory extends Factory
{
    private const TOURNAMENT_NAMES = [
        'world_cup' => ['name' => 'FIFA World Cup', 'country' => 'World'],
        'euro' => ['name' => 'UEFA Euro Cup', 'country' => 'World'],
        'serie_a' => ['name' => 'Serie A', 'country' => 'Italy'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var array{name: string, country: string, type: string} $tournament */
        $tournament = $this->faker->randomElement(self::TOURNAMENT_NAMES);

        $dateTime = $this->faker->dateTimeBetween('+1 week', '+2 weeks');

        return [
            'logo' => $this->faker->imageUrl(40, 80, 'sports', true),
            'season' => $this->faker->year,
            'api_id' => $this->faker->randomNumber(),
            'started_at' => $dateTime,
            'final_started_at' => (clone $dateTime)->add(new DateInterval('P30D')),
            'name' => $tournament['name'],
            'country' => $tournament['country'],
            'is_cup' => 'Serie A' !== $tournament['name'],
        ];
    }

    /**
     * @return Factory<Tournament>
     */
    public function worldCup(): Factory
    {
        $worldCup = self::TOURNAMENT_NAMES['world_cup'];

        return $this->state(fn (): array => [
            'name' => $worldCup['name'],
            'country' => $worldCup['country'],
            'is_cup' => true,
        ]);
    }

    /**
     * @return Factory<Tournament>
     */
    public function euro(): Factory
    {
        $euro = self::TOURNAMENT_NAMES['euro'];

        return $this->state(fn (): array => [
            'name' => $euro['name'],
            'country' => $euro['country'],
            'is_cup' => true,
        ]);
    }

    /**
     * @return Factory<Tournament>
     */
    public function serieA(): Factory
    {
        $serieA = self::TOURNAMENT_NAMES['serie_a'];

        return $this->state(fn (): array => [
            'name' => $serieA['name'],
            'country' => $serieA['country'],
            'is_cup' => false,
        ]);
    }
}
