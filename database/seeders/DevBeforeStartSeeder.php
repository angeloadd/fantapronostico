<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;

final class DevBeforeStartSeeder extends DevBaseSeeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $finishedCount = 0;

        $tournamentStart = $this->computeStartedAt(0, $finishedCount, $now);
        $finalStart = $this->computeStartedAt(51, $finishedCount, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league = $this->createLeague($tournament);
        $teams = $this->createTeamsAndPlayers($tournament);
        $users = $this->createUsers($league);

        // user3 + first 5 faker users (collection indices 5-9) have no champion pick yet
        $skipEmails = array_merge(
            ['user3@fp.test'],
            $users->slice(5)->take(5)->pluck('email')->all()
        );

        foreach (self::SCHEDULE as $i => $slot) {
            $this->createGame(
                $teams[$slot['home']],
                $teams[$slot['away']],
                $slot['stage'],
                $this->computeStartedAt($i, $finishedCount, $now),
                $tournament,
                'not_started'
            );
        }

        $this->createChampions($users, $teams, $skipEmails);
        // No predictions, goals, or ranks — tournament has not started
    }
}
