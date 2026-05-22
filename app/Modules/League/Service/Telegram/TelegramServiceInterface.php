<?php

declare(strict_types=1);

namespace App\Modules\League\Service\Telegram;

use App\Modules\League\Dto\TelegramReminderViewDto;
use Telegram\Bot\Exceptions\TelegramSDKException;

interface TelegramServiceInterface
{
    /**
     * @param  array<int, TelegramReminderViewDto>  $dtos
     *
     * @throws TelegramSDKException
     */
    public function sendReminder(int $chatId, array $dtos): void;

    public function sendRoundPhaseReminder(int $chatId): void;
}
