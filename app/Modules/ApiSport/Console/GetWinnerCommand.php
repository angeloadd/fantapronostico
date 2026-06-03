<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Request\GetWinnerRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

final class GetWinnerCommand extends Command
{
    protected $signature = 'fp:winner:get';

    protected $description = 'Get tournament winner from API Sport and persist it';

    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger): int
    {
        $league = League::first();

        if (null === $league) {
            $logger->error('No league found');

            return 1;
        }

        $dto = $apiSportService->getWinner(new GetWinnerRequest($league->tournament->api_id, $league->tournament->season));

        Team::setWinner($dto, $league->tournament);

        return 0;
    }
}