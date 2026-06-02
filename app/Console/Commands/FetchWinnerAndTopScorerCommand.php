<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Mappers\Apisport\TopScorers;
use App\Helpers\Mappers\Apisport\Winner;
use App\Models\Player;
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
    protected $signature = 'fp:fetch:champions';

    protected $description = 'Fetch tournament winner and top scorers from API Sport and persist them';

    public function handle(ApiSportClientInterface $apisport): int
    {
        $tournament = Tournament::where('api_id', 4)->first();
        if (!$tournament instanceof Tournament) {
            $this->error('Tournament not found');

            return self::FAILURE;
        }

        try {
            $fixturesResponse = $apisport->get('fixtures', ['league' => 4, 'season' => 2024, 'round' => 'Final']);
            $winner = Winner::fromArray($fixturesResponse['response']);
            unset($fixturesResponse);

            $topScorersResponse = $apisport->get('players/topscorers', ['league' => 4, 'season' => 2024]);
            $topScorers = TopScorers::fromArray($topScorersResponse['response']);
            unset($topScorersResponse);
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
                $winnerTeam = Team::whereApiId($winner->toInt())->first();
                if ($winnerTeam instanceof Team) {
                    $tournament->teams()->updateExistingPivot($winnerTeam->id, ['is_winner' => true]);
                }
            }

            foreach ($topScorers->toArray() as $scorerData) {
                $player = Player::find($scorerData['id']);
                if ($player instanceof Player) {
                    $tournament->players()->updateExistingPivot($player->id, ['is_top_scorer' => true]);
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