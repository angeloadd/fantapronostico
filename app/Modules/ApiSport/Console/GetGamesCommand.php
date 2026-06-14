<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Repository\ApiSportGameRepositoryInterface;
use App\Modules\ApiSport\Request\GetGamesRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetGamesCommand extends Command
{
    private const string OUTPUT = '%s: Successfully updated %s games';

    /**
     * @var string
     */
    protected $signature = 'fp:games:get';

    /**
     * @var string
     */
    protected $description = 'Get games from api sports by season and league';

    /**
     * @throws Throwable
     */
    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger, ApiSportGameRepositoryInterface $gameRepository): int
    {
        $league = League::first();

        if (null === $league) {
            $logger->error('No league found in command: '.self::class);

            return 1;
        }

        $gamesDto = $apiSportService->getGamesBySeasonAndLeague(new GetGamesRequest($league->tournament->api_id, $league->tournament->season));

        DB::beginTransaction();
        try {
            $gameRepository->upsertMany($gamesDto);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->error('Error updating games: '.$e->getMessage());
            $logger->error('Error updating games: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return self::FAILURE;
        }

        DB::commit();

        $numberOfGames = count($gamesDto->games());
        $logger->info(sprintf(self::OUTPUT, 'logger', $numberOfGames));
        $this->info(sprintf(self::OUTPUT, 'console', $numberOfGames));

        return self::SUCCESS;
    }
}
