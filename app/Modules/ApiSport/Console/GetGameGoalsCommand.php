<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Enums\GameStatus;
use App\Events\GameGoalsUpdated;
use App\Models\Game;
use App\Modules\ApiSport\Request\GetGameEventsRequest;
use App\Modules\ApiSport\Request\GetGameStatusRequest;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Throwable;

final class GetGameGoalsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fp:games:goals:get';

    /**
     * @var string
     */
    protected $description = 'Get game goals from ApiSport';

    public function handle(ApiSportServiceInterface $apiSportService, LoggerInterface $logger): int
    {
        $league = League::first();

        if (null === $league) {
            $logger->error('No league found');

            return 1;
        }

        $ongoingGames = Game::whereTournamentId($league->tournament->id)
            ->whereStatus(GameStatus::ONGOING)
            ->get();

        foreach ($ongoingGames as $game) {
            try {
                $gameStatus = $apiSportService->getGameStatus(new GetGameStatusRequest($game->id));

                if (GameStatus::FINISHED !== $gameStatus->status) {
                    continue;
                }

                $gameGoals = $apiSportService->getGameGoals(new GetGameEventsRequest($game->id));

                $game->addGameGoals($gameGoals);

                $game->update([
                    'status' => GameStatus::FINISHED,
                ]);

                $logger->info('Updated game goals: '.$game->home_team->name.' vs '.$game->away_team->name);
                $this->info('Updated game goals: '.$game->home_team->name.' vs '.$game->away_team->name);
            } catch (Throwable $e) {
                $logger->error('Error updating game goals: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
                $this->error('Error updating game goals: '.$e->getMessage());

                continue;
            }
        }

        if ($ongoingGames->count() > 0) {
            GameGoalsUpdated::dispatch($league);
        }

        return 0;
    }
}
