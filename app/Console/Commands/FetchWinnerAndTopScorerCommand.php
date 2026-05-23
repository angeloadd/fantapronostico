<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Mappers\Apisport\Winner;
use App\Models\Tournament;
use App\Modules\ApiSport\Client\ApiSportClientInterface;
use App\Modules\ApiSport\Exceptions\ExternalSystemUnavailableException;
use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use App\Modules\Tournament\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class FetchWinnerAndTopScorerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fp:fetch:champions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ApiSportClientInterface $apisport): int
    {
        $winner = 0;
        try {
            $response = $apisport->get('fixtures', ['league' => 4, 'season' => 2024, 'round' => 'Final']);
            $winner = Winner::fromArray($response['response']);
            unset($response);
        } catch (ExternalSystemUnavailableException|InvalidApisportTokenException $e) {
            Log::error(
                'Failed to fetch: '.$e->getMessage(),
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            $this->error('Failed to fetch: '.$e->getMessage());

            return self::FAILURE;
        }

        try {
            if ($winner->toInt()) {
                $winner = Team::find($winner->toInt());
                if ($winner) {
                    Tournament::first()?->teams()->updateExistingPivot($winner->id, ['is_winner' => true]);
                    $winner->save();
                }
            }
        } catch (Throwable $e) {
            Log::error(
                'Internal error: '.$e->getMessage(),
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            $this->error('Internal error: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
