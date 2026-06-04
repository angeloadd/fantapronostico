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
        $tournament = Tournament::createOrFirst([
            'country' => 'World',
            'name' => 'FIFA World Cup',
            'logo' => 'https://media.api-sports.io/football/leagues/1.png',
            'is_cup' => true,
            'season' => 2026,
            'api_id' => 1,
            'started_at' => new DateTimeImmutable('2026-06-11 19:00:00'),
            'final_started_at' => new DateTimeImmutable('2026-07-19 19:00:00'),
        ]);

        $tournament->leagues()->createOrFirst([
            'name' => 'Fantapronostico2026',
        ]);

        return self::SUCCESS;
    }
}
