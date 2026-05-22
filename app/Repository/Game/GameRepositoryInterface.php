<?php

declare(strict_types=1);

namespace App\Repository\Game;

use App\Models\Game;
use DateTimeInterface;
use Illuminate\Support\Collection;

interface GameRepositoryInterface
{
    public function areGameTeamsSet(Game $game): bool;

    /**
     * @return Collection<int, Game>
     */
    public function getLastResults(DateTimeInterface $dateTime): Collection;

    public function getNextGame(): ?Game;

    public function getNextGameByOtherGame(Game $game): ?Game;

    public function getPreviousGameByOtherGame(Game $game): ?Game;

    /**
     * @return Collection<int, Game>
     */
    public function getAll(): Collection;
}
