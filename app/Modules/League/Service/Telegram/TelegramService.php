<?php

declare(strict_types=1);

namespace App\Modules\League\Service\Telegram;

use App\Models\Tournament;
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
            $tournament = Tournament::first();
            if (null === $tournament) {
                return;
            }

            $knockoutsStartFormatted = $tournament->knockouts_started_at?->avoidMutation()?->timezone('Europe/Rome')?->isoFormat('dddd DD \a\l\l\e HH');

            if (null === $knockoutsStartFormatted) {
                return;
            }

            $bot = Telegram::bot('fpbot');
            $bot->sendMessage([
                'chat_id' => $chatId,
                'text' => sprintf(
                    <<<'TEXT'
<strong>%s inizierà la fase finale dell'%s.</strong>

Si potrà pronosticare per ogni partita, oltre che al risultato e segno,
anche un gol per squadra. Si potrà quindi indicare un giocatore dalla lista squadra disponibile per ogni pronostico o in alternativa
la possibilità che una squadra non segni o che faccia un gol grazie all'autogol di un giocatore avversario.
I risultati esatti delle partite varranno inoltre sui 120' escludendo quindi eventuali rigori.
Per ulteriori informazioni vi invito a visitare la sezione regolamento: %s/terms
TEXT,
                    ucfirst($knockoutsStartFormatted),
                    __($tournament->name),
                    config('app.url')
                ),
                'parse_mode' => 'HTML',
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error sending round phase reminder: '.$e->getMessage(), ['exception' => $e]);
        }
    }
}
