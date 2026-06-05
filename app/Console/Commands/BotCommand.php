<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Game;
use App\Modules\League\Dto\TelegramReminderViewDto;
use App\Modules\League\Service\Telegram\TelegramServiceInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class BotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fp:bot:telegram {gameId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(TelegramServiceInterface $telegramService): int
    {
        foreach ($this->getRoundPhaseReminderTimes() as $roundPhaseReminderTime) {
            if (abs(now()->unix() - $roundPhaseReminderTime->unix()) < 60) {
                $telegramService->sendRoundPhaseReminder(-1001766446905);
            }
        }

        if ($this->argument('gameId')) {
            $games = collect([Game::find($this->argument('gameId'))]);
        } else {
            $games = $this->getGamesFromTo(59, 60);

            if ($games->isEmpty()) {
                $games = $this->getGamesFromTo((60 * 23) + 59, 60 * 24);
            }

            if ($games->isEmpty()) {
                return self::SUCCESS;
            }
        }

        /** @var array<int, TelegramReminderViewDto> $dtos */
        $dtos = $games->map(
            static fn (Game $game) => new TelegramReminderViewDto(
                $game->id,
                $game->home_team->name ?? '',
                $game->away_team->name ?? '',
                (string) str($game->started_at->avoidMutation()->timezone('Europe/Rome')->isoFormat('\e\n\t\r\o \i\l D MMMM YYYY \a\l\l\e HH:mm'))->title()
            )
        )->toArray();

        try {
            $telegramService->sendReminder(-1001766446905, $dtos);

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::channel('schedule')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return self::FAILURE;
        }
    }

    /**
     * @return array<int, Carbon>
     */
    private function getRoundPhaseReminderTimes(): array
    {
        return [
            Carbon::parse('2026-06-26 21:00:00'), // 2 eves before R32 (starts Jun 28)
            Carbon::parse('2026-06-27 21:00:00'), // 1 eve before R32
            Carbon::parse('2026-06-28 09:00:00'), // morning of R32 start
        ];

    }

    /**
     * @return Collection<int, Game>
     */
    private function getGamesFromTo(int $from, int $to): Collection
    {
        return Game::whereBetween('started_at', [now()->addMinutes($from), now()->addMinutes($to)])->get();
    }
}
