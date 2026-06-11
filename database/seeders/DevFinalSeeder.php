<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

final class DevFinalSeeder extends DevBaseSeeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $finishedCount = 52;

        $tournament = $this->createTournament($now, $finishedCount);
        $league = $this->createLeague($tournament);
        $teams = $this->createTeamsAndPlayers($tournament);
        $users = $this->createUsers($league);

        $this->createChampions($users, $teams, $league);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam = $teams[$slot['home']];
            $awayTeam = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, $finishedCount, $now);

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, 'finished');
            $this->createGoalsForGame($game, $homeTeam, $awayTeam);
            $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
        }

        // Mark tournament winner and top scorer on pivot tables
        $winnerTeam = $teams[self::WINNER_TEAM_INDEX];
        $topScorerPlayer = $winnerTeam->players->values()->get(self::TOP_SCORER_PLAYER_LOCAL_INDEX);

        $tournament->teams()->updateExistingPivot($winnerTeam->id, ['is_winner' => true]);
        $tournament->players()->updateExistingPivot($topScorerPlayer->id, ['is_top_scorer' => true]);

        Artisan::call('fp:ranking:calculate-view');
    }
}
