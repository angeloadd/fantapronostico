<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

final class DevGroupPhaseSeeder extends DevBaseSeeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $finishedCount = 24;

        $tournament = $this->createTournament($now, $finishedCount);
        $league = $this->createLeague($tournament);
        $teams = $this->createTeamsAndPlayers($tournament);
        $users = $this->createUsers($league);

        $this->createChampions($users, $teams, $league);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam = $teams[$slot['home']];
            $awayTeam = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, $finishedCount, $now);
            $status = $i < $finishedCount ? 'finished' : 'not_started';

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, $status);

            if ('finished' === $status) {
                $this->createGoalsForGame($game, $homeTeam, $awayTeam);
                $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
            }
        }

        Artisan::call('fp:ranking:calculate-view', ['--leagueId' => $league->id]);
    }
}
