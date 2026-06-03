<?php

declare(strict_types=1);

namespace App\Modules\League\Service\Telegram;

use App\Modules\League\Dto\TelegramReminderViewDto;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

final class TelegramService implements TelegramServiceInterface
{
    public function __construct(private readonly LoggerInterface $logger) {}

    /**
     * @param  TelegramReminderViewDto[]  $dtos
     *
     * @throws TelegramSDKException
     */
    public function sendReminder(int $chatId, array $dtos): void
    {
        $bot = Telegram::bot('fpbot');
        foreach ($dtos as $dto) {
            $bot->sendMessage([
                'chat_id' => $chatId,
                'text' => view('telegram.game', compact('dto'))->render(),
                'parse_mode' => 'HTML',
            ]);
        }

    }

    public function sendRoundPhaseReminder(int $chatId): void
    {
        try {

            $bot = Telegram::bot('fpbot');
            $bot->sendMessage([
                'chat_id' => $chatId,
                'text' => <<<'TEXT'
<strong>Sabato alle 18 inizierà la fase finale dell'Europeo 2024.</strong>

Si potrà pronosticare per ogni partita, oltre che al risultato e segno,
anche un gol per squadra. Si potrà quindi indicare un giocatore dalla lista squadra disponibile per ogni pronostico o in alternativa
la possibilità che una squadra non segni o che faccia un gol grazie all'autogol di un giocatore avversario.
I risultati esatti delle partite varranno inoltre sui 120' escludendo quindi eventuali rigori.
Per ulteriori informazioni vi invito a visitare la sezione regolamento: https://fantapronostico.com/terms
TEXT,
                'parse_mode' => 'HTML',
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error sending round phase reminder: '.$e->getMessage(), ['exception' => $e]);
        }
    }
}
