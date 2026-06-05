<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

final class DevKnockoutPhaseSeeder extends DevBaseSeeder
{
    private const FINISHED_COUNT = 40;

    public function run(): void
    {
        $now = Carbon::now();

        $tournamentStart = $this->computeStartedAt(0, self::FINISHED_COUNT, $now);
        $finalStart = $this->computeStartedAt(51, self::FINISHED_COUNT, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league = $this->createLeague($tournament);
        $teams = $this->createTeamsAndPlayers($tournament);
        $users = $this->createUsers($league);

        $this->createChampions($users, $teams, $league);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam = $teams[$slot['home']];
            $awayTeam = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, self::FINISHED_COUNT, $now);
            $status = $i < self::FINISHED_COUNT ? 'finished' : 'not_started';

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, $status);

            if ('finished' === $status) {
                $this->createGoalsForGame($game, $homeTeam, $awayTeam);
                $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
            }
        }

        Artisan::call('fp:ranking:calculate-view', ['--leagueId' => $league->id]);
    }
}
