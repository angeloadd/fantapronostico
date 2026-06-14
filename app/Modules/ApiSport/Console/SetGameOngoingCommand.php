<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Console;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

final class SetGameOngoingCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fp:games:set-ongoing';

    /**
     * @var string
     */
    protected $description = 'Set games to ongoing if started_at is in the past';

    public function handle(LoggerInterface $logger): int
    {
        $league = League::first();
        if (null === $league) {
            $logger->error('No league found in command: '.self::class);

            return 1;
        }

        DB::beginTransaction();
        try {
            $games = Game::whereTournamentId($league->tournament->id)
                ->whereStatus(GameStatus::NOT_STARTED)
                ->where('started_at', '<', now())
                ->get();

            foreach ($games as $game) {
                $game->update([
                    'status' => GameStatus::ONGOING,
                ]);
            }

        } catch (Throwable $e) {
            DB::rollBack();
            $logger->error('Error updating games: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('Error updating games: '.$e->getMessage());

            return 1;
        }
        DB::commit();

        $logger->info('logger: Set games to ongoing: '.$games->count());
        $this->info('console: Set games to ongoing: '.$games->count());

        return 0;
    }
}
