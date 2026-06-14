<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Models\Player;
use App\Modules\ApiSport\Request\GetTopScorersRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetTopScorersCommand extends Command
{
    private const string OUTPUT = '%s: Successfully updated tournament top scorers';

    /**
     * @var string
     */
    protected $signature = 'fp:topscorers:get';

    /**
     * @var string
     */
    protected $description = 'Get winner from api sport';

    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger): int
    {
        $league = League::first();

        if (null === $league) {
            $logger->error('No league found in command: '.self::class);

            return 1;
        }

        $dto = $apiSportService->getTopScorers(
            new GetTopScorersRequest(
                $league->tournament->api_id,
                $league->tournament->season
            )
        );

        DB::beginTransaction();
        try {
            Player::setTopScorers($dto, $league->tournament);
        } catch (Throwable $exception) {
            DB::rollBack();
            $logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $this->error('Error updating top scorers: '.$exception->getMessage());
        }
        DB::commit();

        $this->info(sprintf(self::OUTPUT, 'console'));
        $logger->info(sprintf(self::OUTPUT, 'logger'));

        return 0;
    }
}
