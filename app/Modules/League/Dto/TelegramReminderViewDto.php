<?php

declare(strict_types=1);

namespace App\Modules\League\Dto;

final readonly class TelegramReminderViewDto
{
    public function __construct(
        public int $gameId,
        public string $homeTeamName,
        public string $awayTeamName,
        public string $formattedBefore
    ) {}
}
