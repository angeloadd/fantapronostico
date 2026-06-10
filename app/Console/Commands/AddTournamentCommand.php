<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tournament;
use DateTimeImmutable;
use Exception;
use Illuminate\Console\Command;

final class AddTournamentCommand extends Command
{
    protected $signature = 'fp:tournament:add';

    protected $description = 'Add a new tournament';

    /**
     * @throws Exception
     */
    public function handle(): int
    {
        $tournament = Tournament::updateOrCreate(
            ['season' => 2026, 'api_id' => 1],
            [
                'country' => 'World',
                'logo' => 'https://media.api-sports.io/football/leagues/1.png',
                'is_cup' => true,
                'season' => 2026,
                'started_at' => new DateTimeImmutable('2026-06-11 19:00:00'),
                'final_started_at' => new DateTimeImmutable('2026-07-19 19:00:00'),
                'knockouts_started_at' => new DateTimeImmutable('2026-06-28 19:00:00'),
                'api_id' => 1,
                'name' => 'FIFA World Cup',
            ],
        );

        $tournament->leagues()->updateOrCreate(['name' => 'Fantapronostico2026']);

        return self::SUCCESS;
    }
}
