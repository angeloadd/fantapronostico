<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Dto\NationalDto;
use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use App\Modules\ApiSport\Repository\ApiSportPlayerRepositoryInterface;
use App\Modules\ApiSport\Request\GetPlayersByNationalRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetPlayersByTeamCommand extends Command
{
    private const OUTPUT = '%s: successfully updated %d players';

    protected $signature = 'fp:players:get';

    protected $description = 'get from apisport players by team';

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
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
