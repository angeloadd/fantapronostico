<?php

declare(strict_types=1);

namespace App\Repository\Game;

use App\Models\Game;
use App\Modules\ApiSport\Dto\GamesDto;
use App\Modules\ApiSport\Repository\ApiSportGameRepositoryInterface;
use DateTimeInterface;
use Illuminate\Support\Collection;

final class GameRepository implements ApiSportGameRepositoryInterface, GameRepositoryInterface
{
    public function getNextGame(): ?Game
    {
        $game = Game::firstWhere('started_at', '>', now());

        return $game instanceof Game ? $game : null;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getAll(): Collection
    {
        return Game::all();
    }

    public function areGameTeamsSet(Game $game): bool
    {
        return null !== $game->home_team && null !== $game->away_team;
    }

    public function getPreviousGameByOtherGame(Game $game): ?Game
    {
        $gamesFromDb = Game::orderBy('id')
            ->where('started_at', $game->started_at)->get();

        if (0 === $gamesFromDb->count()) {
            return null;
        }

        if ($gamesFromDb->count() > 1 && $game->id === $gamesFromDb->last()?->id) {
            return $gamesFromDb->first();
        }

        return Game::fromLatest()->where('started_at', '<', $game->started_at)->first();
    }

    public function getNextGameByOtherGame(Game $game): ?Game
    {
        $gamesFromDb = Game::orderBy('id')
            ->where('started_at', $game->started_at)->get();

        if (0 === $gamesFromDb->count()) {
            return null;
        }

        if ($gamesFromDb->count() > 1 && $game->id === $gamesFromDb->first()?->id) {
            return $gamesFromDb->last();
        }

        return Game::where('started_at', '>', $game->started_at)->first();
    }

    /**
     * @return Collection<int, Game>
     */
    public function getLastResults(DateTimeInterface $dateTime): Collection
    {
        return Game::lastResults($dateTime)->get();
    }

    public function upsertMany(GamesDto $games): void
    {
        Game::upsertMany($games);
    }
}
