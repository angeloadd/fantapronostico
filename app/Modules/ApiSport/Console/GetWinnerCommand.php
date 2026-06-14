<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Modules\ApiSport\Request\GetWinnerRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetWinnerCommand extends Command
{
    private const string OUTPUT = '%s: Successfully updated tournament winner';

    /**
     * @var string
     */
    protected $signature = 'fp:winner:get';

    /**
     * @var string
     */
    protected $description = 'Get tournament winner from API Sport and persist it';

    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger): int
    {
        $league = League::first();

        if (null === $league) {
            $logger->error('No league found in command: '.self::class);

            return 1;
        }

        $dto = $apiSportService->getWinner(new GetWinnerRequest($league->tournament->api_id, $league->tournament->season));

        DB::beginTransaction();
        try {
            Team::setWinner($dto, $league->tournament);
        } catch (Throwable $exception) {
            DB::rollBack();
            $logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $this->error('Error updating winner: '.$exception->getMessage());
        }
        DB::commit();

        $this->info(sprintf(self::OUTPUT, 'console'));
        $logger->info(sprintf(self::OUTPUT, 'logger'));

        return 0;
    }
}
