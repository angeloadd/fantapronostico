<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use App\Enums\GameStatus;
use App\Models\Champion;
use App\Models\Game;
use App\Models\Prediction;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use stdClass;

final readonly class RankingCalculator implements RankingCalculatorInterface
{
    public function __construct(private SorterInterface $sorter, private LoggerInterface $logger) {}

    public function calculate(League $league): void
    {
        $league->users
            ->filter(static fn (User $user) => 'accepted' === $user->pivot->status)
            ->each(
                function (User $user) use ($league): void {
                    $rank = $this->calculateUserRank($user, $league);

                    $oldRank = DB::table('ranks')
                        ->where('user_id', $rank->userId())
                        ->first();

                    if ($rank->total > 0) {
                        $this->logger->info(
                            sprintf(
                                'Updating user %s rank current %d + new %d = %d',
                                $user->name,
                                $oldRank?->total ?? 0,
                                $rank->total(),
                                $rank->total() + ($oldRank?->total ?? 0)
                            )
                        );
                    }

                    DB::table('ranks')->updateOrInsert(
                        ['user_id' => $rank->userId()],
                        [
                            'user_id' => $rank->userId(),
                            'league_id' => $league->id,
                            'total' => $rank->total() + ($oldRank?->total ?? 0),
                            'results' => $rank->results() + ($oldRank?->results ?? 0),
                            'scorers' => $rank->scorers() + ($oldRank?->scorers ?? 0),
                            'signs' => $rank->signs() + ($oldRank?->signs ?? 0),
                            'final_total' => $rank->finalPredictionTotal(),
                            'final_timestamp' => 0 === $rank->finalPredictionTimestamp() ? null : Carbon::createFromTimestamp($rank->finalPredictionTimestamp()),
                            'winner' => $rank->winner(),
                            'top_scorer' => $rank->topScorer(),
                            'from' => $rank->latestGameStartedAt ?? $oldRank?->from,
                        ]
                    );

                    unset($rank, $oldRank, $user->predictions);
                }
            );
    }

    public function get(League $league): Collection
    {
        $ranking = DB::table('ranks')
            ->where('league_id', $league->id)
            ->get()
            ->map(
                static function (stdClass $rank) use ($league) {
                    $user = User::find($rank->user_id);

                    if (!$user instanceof User) {
                        return new UserRank($rank->user_id, 'unknown', $league->id);
                    }

                    return new UserRank(
                        $rank->user_id,
                        $user->name,
                        $league->id,
                        $rank->total,
                        $rank->results,
                        $rank->signs,
                        $rank->scorers,
                        $rank->final_total ?? 0,
                        null !== $rank->final_timestamp ? Carbon::parse($rank->final_timestamp)->unix() : 0,
                        $rank->winner,
                        $rank->top_scorer
                    );
                }
            );

        return $this->sorter->sortAggregate($ranking);
    }

    private function calculateUserRank(User $user, League $league): UserRank
    {
        $rank = new UserRank(
            $user->id,
            $user->name,
            $league->id
        );

        $rank = $this->calculatePredictionScores($user, $league, $rank);

        return $this->addWinnerAndTopScorerScores($user, $league, $rank);
    }

    private function calculatePredictionScores(User $user, League $league, UserRank $rank): UserRank
    {
        $predictions = $user->predictions
            ->whereStrict('league_id', $league->id)
            ->filter(fn (Prediction $prediction) => GameStatus::FINISHED === $prediction->game->status)
            ->filter(static function (Prediction $prediction): bool {
                $userRank = DB::table('ranks')->where('user_id', $prediction->user_id)->first();

                return null === $userRank ||
                    null === $userRank->from ||
                    Carbon::parse($userRank->from)->lt($prediction->game->started_at);
            })
            ->map(fn (Prediction $prediction) => [
                'latest_game_started_at' => Game::whereStatus(GameStatus::FINISHED)
                    ->whereTournamentId($league->tournament_id)
                    ->orderBy('started_at', 'desc')
                    ->first()?->started_at,
                'score' => PredictionScoreFactory::create($prediction),
            ]);

        return $predictions->reduce(function (UserRank $rank, array $result): UserRank {
            $prediction = $result['score'];
            if ($prediction->result) {
                $rank->numberOfResults++;
            }

            if ($prediction->sign) {
                $rank->numberOfSigns++;
            }

            if ($prediction->homeScorer) {
                $rank->numberOfScorers++;
            }

            if ($prediction->awayScorer) {
                $rank->numberOfScorers++;
            }

            if ($prediction->isFinal) {
                $rank->finalBetTimestamp = $prediction->timestamp;
                $rank->finalBetTotal = $prediction->total();
            }

            $rank->total += $prediction->total();

            if ($rank->total > 0) {
                $rank->latestGameStartedAt = $result['latest_game_started_at'];
            }

            return $rank;
        }, $rank);
    }

    private function addWinnerAndTopScorerScores(User $user, League $league, UserRank $rank): UserRank
    {
        $winner = Team::find(9);
        if (!$winner instanceof Team) {
            return $rank;
        }

        $champion = $user->champion;
        if (!$champion instanceof Champion) {
            return $rank;
        }

        if (4 === $champion->team_id) {
            $rank->winner = true;
            $rank->total += 15;
        }

        if (in_array($champion->player_id, [184, 180496, 1323, 247, 181812, 15328])) {
            $rank->topScorer = true;
            $rank->total += 10;
        }

        return $rank;
    }
}
