<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\GameStatus;
use App\Helpers\Ranking\RankingCalculatorInterface;
use App\Helpers\Ranking\UserRank;
use App\Models\Game;
use App\Models\Tournament;
use App\Modules\Auth\Repository\UserRepositoryInterface;
use App\Modules\League\Models\League;
use App\Repository\Game\GameRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class HomeController extends Controller
{
    public function __construct(
        private readonly RankingCalculatorInterface $calculator,
        private readonly GameRepositoryInterface $gameRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function index(): Renderable
    {
        $user = $this->userRepository->getAuthenticatedUser();

        $league = $user->selectedLeague ?? $user->leagues->first();

        if (!$league instanceof League) {
            throw new UnauthorizedHttpException('You shall not pass!!');
        }

        $tournament = $league->tournament;
        $ranking = $this->calculator->get($league);

        $latestGames = Game::where('started_at', '>', now())
            ->get()
            ->groupBy('started_at')
            ->first();

        $nextGame = $latestGames?->filter(
            static fn (Game $game) => null === $game->predictions->firstWhere('user_id', $user->id)
        )?->first() ?? $latestGames?->last();

        return view('pages.home.index', [
            'ranking' => $ranking->filter(static fn (UserRank $rank, int $index) => $user->id === $rank->userId() || $index <= 9),
            'nextGame' => $nextGame,
            'champion' => $user->champion,
            'hasFinalStarted' => $this->hasFinalStarted($tournament),
            'lastResults' => $this->gameRepository->getLastResults(now())->take(3),
            'isWinnerDeclared' => $this->isWinnerDeclared($tournament),
            'winnerName' => $ranking->first()?->userName() ?? '',
            'leagueName' => $league->name,
            'games' => $this->gameRepository->getAll(),
            'userRank' => $ranking->filter(static fn (UserRank $rank) => $rank->userId() === $user->id)->first(),
            'tournamentStartedAt' => $tournament->started_at->toImmutable(),
        ]);
    }

    private function isWinnerDeclared(Tournament $tournament): bool
    {
        return $tournament->final_started_at->isPast() &&
            $tournament->games->every('status', '=', GameStatus::FINISHED);
    }

    private function hasFinalStarted(Tournament $tournament): bool
    {
        return $tournament->final_started_at->isPast();
    }
}
