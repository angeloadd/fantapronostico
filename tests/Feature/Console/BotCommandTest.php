<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Game;
use App\Models\Tournament;
use App\Modules\League\Dto\TelegramReminderViewDto;
use App\Modules\League\Models\League;
use App\Modules\League\Service\Telegram\TelegramServiceInterface;
use App\Modules\Tournament\Models\Team;
use Carbon\Carbon;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class BotCommandTest extends TestCase
{
    private MockObject&TelegramServiceInterface $telegramService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->forgetInstance(TelegramServiceInterface::class);
        $this->telegramService = $this->createMock(TelegramServiceInterface::class);

        $this->app->extend(TelegramServiceInterface::class, fn () => $this->telegramService);
    }

    public function test_handle(): void
    {
        $now = new DateTimeImmutable('2024-06-25 17:00:00');
        Carbon::setTestNow($now);
        $tournament = Tournament::factory()->create();
        League::create([
            'tournament_id' => $tournament->id,
            'name' => 'test',
        ]);
        $game = Game::factory()->create([
            'started_at' => '2024-06-25 18:00:00',
            'tournament_id' => $tournament->id,
        ]);
        $home = Team::factory()->create();
        $away = Team::factory()->create();
        $game->teams()->attach([
            $home->id => ['is_away' => false],
            $away->id => ['is_away' => true],
        ]);

        $telegramReminderViewDtos = [
            new TelegramReminderViewDto(
                $game->id,
                $home->name,
                $away->name,
                'Entro Il 25 Giugno 2024 Alle 18:00'
            ),
        ];
        $this->telegramService->expects(self::once())
            ->method('sendReminder')
            ->with(-1001766446905, $telegramReminderViewDtos);

        $this->artisan('fp:bot:telegram')->assertOk()->run();
    }
}
