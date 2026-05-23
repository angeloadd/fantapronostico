<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Repository\ApiSportGameRepositoryInterface;
use App\Modules\ApiSport\Request\GetGamesRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetGamesCommand extends Command
{
    private const OUTPUT = '%s: Successfully updated %s games';

    /**
     * @var string
     */
    protected $signature = 'fp:games:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get games from api sports by season and league';

    /**
     * @throws Throwable
     */
    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger, ApiSportGameRepositoryInterface $gameRepository): int
    {
        $gamesDto = $apiSportService->getGamesBySeasonAndLeague(new GetGamesRequest(4, 2024));

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
