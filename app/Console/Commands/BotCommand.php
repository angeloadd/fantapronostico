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
    private const int CHAT_ID = -1001766446905;

    protected $signature = 'fp:bot:telegram {gameId?}';

    protected $description = 'Send Telegram reminders for upcoming games';

    public function handle(TelegramServiceInterface $telegramService): int
    {
        foreach ($this->getRoundPhaseReminderTimes() as $roundPhaseReminderTime) {
            if (abs(now()->unix() - $roundPhaseReminderTime->unix()) < 60) {
                $telegramService->sendRoundPhaseReminder(self::CHAT_ID);
            }
        }

        if ($this->argument('gameId')) {
            return $this->sendGames($telegramService, collect([Game::find($this->argument('gameId'))]));
        }

        // 24h reminder: all games
        $this->sendWindow($telegramService, 60 * 24);

        // 1h reminder: before-midnight games (kick-off hour >= 06:00)
        $this->sendWindow($telegramService, 60, fn (Game $g) => !$this->isAfterMidnight($g->started_at));

        // 8h reminder: after-midnight games (kick-off hour < 06:00)
        $this->sendWindow($telegramService, 60 * 8, fn (Game $g) => $this->isAfterMidnight($g->started_at));

        return self::SUCCESS;
    }

    private function sendWindow(
        TelegramServiceInterface $telegramService,
        int $minutesAhead,
        ?callable $filter = null,
    ): void {
        $games = Game::whereBetween('started_at', [
            now()->addMinutes($minutesAhead - 1),
            now()->addMinutes($minutesAhead),
        ])->get();

        if (null !== $filter) {
            $games = $games->filter($filter)->values();
        }

        if ($games->isEmpty()) {
            return;
        }

        try {
            $telegramService->sendReminder(self::CHAT_ID, $this->buildDtos($games));
        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::channel('schedule')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    private function sendGames(TelegramServiceInterface $telegramService, Collection $games): int
    {
        try {
            $telegramService->sendReminder(self::CHAT_ID, $this->buildDtos($games));

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::channel('schedule')->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return self::FAILURE;
        }
    }

    /** @return array<int, TelegramReminderViewDto> */
    private function buildDtos(Collection $games): array
    {
        return $games->map(
            static fn (Game $game) => new TelegramReminderViewDto(
                $game->id,
                $game->home_team->name ?? '',
                $game->away_team->name ?? '',
                (string) str($game->started_at->avoidMutation()->timezone('Europe/Rome')->isoFormat('\e\n\t\r\o \i\l D MMMM YYYY \a\l\l\e HH:mm'))->title()
            )
        )->toArray();
    }

    private function isAfterMidnight(Carbon $startedAt): bool
    {
        return $startedAt->copy()->timezone('Europe/Rome')->hour < 6;
    }

    /** @return array<int, Carbon> */
    private function getRoundPhaseReminderTimes(): array
    {
        return [
            Carbon::parse('2026-06-26 21:00:00'), // 2 eves before R32 (starts Jun 28)
            Carbon::parse('2026-06-27 21:00:00'), // 1 eve before R32
            Carbon::parse('2026-06-28 09:00:00'), // morning of R32 start
        ];
    }
}
