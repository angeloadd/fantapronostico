<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Champion;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class DevFinalSeeder extends DevBaseSeeder
{
    private const FINISHED_COUNT = 52;

    public function run(): void
    {
        $now = Carbon::now();

        $tournamentStart = $this->computeStartedAt(0, self::FINISHED_COUNT, $now);
        $finalStart      = $this->computeStartedAt(51, self::FINISHED_COUNT, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league     = $this->createLeague($tournament);
        $teams      = $this->createTeamsAndPlayers($tournament);
        $users      = $this->createUsers($league);

        $this->createChampions($users, $teams);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam  = $teams[$slot['home']];
            $awayTeam  = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, self::FINISHED_COUNT, $now);

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, 'finished');
            $this->createGoalsForGame($game, $homeTeam, $awayTeam);
            $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
        }

        // Mark tournament winner and top scorer on pivot tables
        $winnerTeam      = $teams[self::WINNER_TEAM_INDEX];
        $topScorerPlayer = $winnerTeam->players->values()->get(self::TOP_SCORER_PLAYER_LOCAL_INDEX);

        $tournament->teams()->updateExistingPivot($winnerTeam->id, ['is_winner' => true]);
        $tournament->players()->updateExistingPivot($topScorerPlayer->id, ['is_top_scorer' => true]);

        // Base rank rows
        $this->createRanks($users, $league, self::FINISHED_COUNT);

        // Apply bonuses for users whose champion pick matched
        $users->each(function (User $user) use ($league, $winnerTeam, $topScorerPlayer): void {
            $champion = Champion::where('user_id', $user->id)->first();
            if (! $champion instanceof Champion) {
                return;
            }

            $isWinner    = $champion->team_id === $winnerTeam->id;
            $isTopScorer = $champion->player_id === $topScorerPlayer->id;

            if (! $isWinner && ! $isTopScorer) {
                return;
            }

            $bonus = ($isWinner ? 15 : 0) + ($isTopScorer ? 10 : 0);

            DB::table('ranks')
                ->where('user_id', $user->id)
                ->where('league_id', $league->id)
                ->update([
                    'winner'     => $isWinner,
                    'top_scorer' => $isTopScorer,
                    'total'      => DB::raw("total + {$bonus}"),
                ]);
        });
    }
}