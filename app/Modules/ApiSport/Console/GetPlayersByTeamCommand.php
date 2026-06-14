<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Dto\NationalDto;
use App\Modules\ApiSport\Repository\ApiSportPlayerRepositoryInterface;
use App\Modules\ApiSport\Request\GetPlayersByNationalRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetPlayersByTeamCommand extends Command
{
    private const string OUTPUT = '%s: successfully updated %d players';

    /**
     * @var string
     */
    protected $signature = 'fp:players:get';

    /**
     * @var string
     */
    protected $description = 'get from apisport players by team';

    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger, ApiSportPlayerRepositoryInterface $playerRepository): int
    {

        /** @var ?GetPlayersByNationalRequest[] $requests */
        $requests = League::first()
            ?->tournament
            ?->teams
            ?->map(fn (Team $team) => new GetPlayersByNationalRequest($team->api_id))
            ?->toArray();

        if (null === $requests) {
            $logger->warning('No league found');

            return self::INVALID;
        }

        $nationalsDto = $apiSportService->getPlayersByNational($requests, 6);

        DB::beginTransaction();
        try {
            $playerRepository->upsertManyByNational($nationalsDto);
        } catch (Throwable $e) {
            DB::rollBack();
            $logger->error(
                'Failed to fetch: '.$e->getMessage(),
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            $this->error('Failed to fetch: '.$e->getMessage());

            return self::FAILURE;
        }

        DB::commit();

        $totalPlayersCount = array_reduce(
            $nationalsDto->nationals(),
            static fn (int $count, NationalDto $national): int => $count + count($national->players()),
            0
        );

        $logger->info(sprintf(self::OUTPUT, 'console', $totalPlayersCount));
        $this->info(sprintf(self::OUTPUT, 'console', $totalPlayersCount));

        return self::SUCCESS;
    }
}
