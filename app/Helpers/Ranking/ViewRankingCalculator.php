<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use App\Enums\GameStatus;
use App\Models\Champion;
use App\Models\Prediction;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\ChampionRank;
use App\Modules\League\Models\League;
use App\Modules\League\Models\PredictionRank;
use App\Modules\Tournament\Models\Team;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use stdClass;

final readonly class ViewRankingCalculator implements RankingCalculatorInterface
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function calculate(League $league): void
    {
        $league->users
            ->filter(static fn (User $user) => 'accepted' === $user->pivot->status)
            ->each(function (User $user) use ($league): void {
                $this->scorePredictions($user, $league);
                $this->scoreChampion($user, $league);
            });

        DB::statement('REFRESH MATERIALIZED VIEW ranking_view');
    }

    public function get(League $league): Collection
    {
        $ranking = DB::table('ranking_view')
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
                        null !== $rank->final_timestamp ? Carbon::parse($rank->final_timestamp)->unix() : 0,
                        $rank->final_total ?? 0,
                        $rank->winner,
                        $rank->top_scorer
                    );
                }
            );

        return $ranking->sortBy('position')->values();
    }

    private function scorePredictions(User $user, League $league): void
    {
        $scoredIds = PredictionRank::where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->pluck('prediction_id');

        $rows = $user->predictions
            ->whereStrict('league_id', $league->id)
            ->filter(fn (Prediction $prediction) => GameStatus::FINISHED === $prediction->game->status)
            ->reject(fn (Prediction $prediction) => $scoredIds->contains($prediction->id))
            ->map(function (Prediction $prediction) use ($user, $league): array {
                $score = PredictionScoreFactory::create($prediction);
                $now = now();

                $total = ($score->result ? ScoringValues::EXACT : 0)
                    + ($score->sign ? ScoringValues::SIGN : 0)
                    + (($score->homeScorer ? 1 : 0) + ($score->awayScorer ? 1 : 0)) * ScoringValues::SCORER;

                $this->logger->info(sprintf(
                    'Prediction[id=%d] for User[id=%d] in League[id=%d] for Game[id=%d] scored %d points',
                    $prediction->id,
                    $user->id,
                    $league->id,
                    $prediction->game_id,
                    $total
                ));

                return [
                    'user_id' => $user->id,
                    'prediction_id' => $prediction->id,
                    'league_id' => $league->id,
                    'game_id' => $prediction->game_id,
                    'is_exact' => $score->result,
                    'is_sign' => $score->sign,
                    'is_home_scorer' => $score->homeScorer,
                    'is_away_scorer' => $score->awayScorer,
                    'value_exact' => ScoringValues::EXACT,
                    'value_sign' => ScoringValues::SIGN,
                    'value_scorer' => ScoringValues::SCORER,
                    'total' => $total,
                    'is_final' => $score->isFinal,
                    'predicted_at' => $prediction->updated_at,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values()
            ->toArray();

        if ([] !== $rows) {
            PredictionRank::insert($rows);
        }
    }

    private function scoreChampion(User $user, League $league): void
    {
        $tournament = $league->tournament;

        if (null === $tournament->final_started_at || $tournament->final_started_at->isFuture()) {
            return;
        }

        $alreadyScored = ChampionRank::where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->exists();

        if ($alreadyScored) {
            return;
        }

        $champion = Champion::where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->first();

        $isWinner = false;
        $isTopScorer = false;

        if ($champion instanceof Champion) {
            $winnerTeam = $tournament->teams()->wherePivot('is_winner', true)->first();
            $topScorerIds = $tournament->players()->wherePivot('is_top_scorer', true)->pluck('players.id');
            $isWinner = $winnerTeam instanceof Team && $winnerTeam->id === $champion->team_id;
            $isTopScorer = $topScorerIds->contains($champion->player_id);
        }

        ChampionRank::create([
            'user_id' => $user->id,
            'league_id' => $league->id,
            'winner' => $isWinner,
            'top_scorer' => $isTopScorer,
            'value_winner' => ScoringValues::WINNER,
            'value_top_scorer' => ScoringValues::TOP_SCORER,
            'total' => ($isWinner ? ScoringValues::WINNER : 0)
                + ($isTopScorer ? ScoringValues::TOP_SCORER : 0),
        ]);
    }
}
