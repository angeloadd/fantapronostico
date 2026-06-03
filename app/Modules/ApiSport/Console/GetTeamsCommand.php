<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use App\Modules\ApiSport\Repository\ApiSportTeamRepositoryInterface;
use App\Modules\ApiSport\Request\GetTeamsRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetTeamsCommand extends Command
{
    private const OUTPUT = '%s: Successfully updated %s teams';

    /**
     * @var string
     */
    protected $signature = 'fp:teams:get';

    /**
     * @var string
     */
    protected $description = 'Get teams from api sports by season and league';

    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws InvalidApisportTokenException
     * @throws ErrorException
     */
    public function handle(
        ApiSportServiceInterface $apiSportService,
        LoggerInterface $logger,
        ApiSportTeamRepositoryInterface $teamRepository
    ): int {
        $league = League::first();

        if (null === $league) {
            $logger->error('No league found');

            return 1;
        }

        $teamsDto = $apiSportService->getTeamsBySeasonAndLeague(new GetTeamsRequest($league->tournament->api_id, $league->tournament->season));

        DB::beginTransaction();

        try {
            $teamRepository->upsertMany($teamsDto);
        } catch (Throwable $exception) {
            DB::rollBack();
            $this->error('Error updating teams: '.$exception->getMessage());
            $logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);

            return self::FAILURE;
        }

        DB::commit();

        $numberOfTeams = count($teamsDto->teams());
        $logger->info(sprintf(self::OUTPUT, 'logger', $numberOfTeams));
        $this->info(sprintf(self::OUTPUT, 'console', $numberOfTeams));

        return self::SUCCESS;
    }
}
