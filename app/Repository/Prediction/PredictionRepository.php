<?php

declare(strict_types=1);

namespace App\Repository\Prediction;

use App\Models\Game;
use App\Models\Prediction;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Collection;

final class PredictionRepository implements PredictionRepositoryInterface
{
    /**
     * @return Collection<int, Prediction>
     */
    public function getSortedDescByUpdatedAtByGame(Game $game): Collection
    {
        return $game->predictions->sortByDesc('updated_at')->values();
    }

    /**
     * @return Collection<int, Prediction>
     */
    public function getByUser(User $user): Collection
    {
        return $user->predictions;
    }

    public function getByGameAndUser(Game $game, User $user): ?Prediction
    {
        return $game->predictions->where('user_id', $user->id)
            ->first();
    }

    public function existsByGameAndUser(Game $game, User $user): bool
    {
        return Prediction::where('game_id', $game->id)->where('user_id', $user->id)->exists();
    }
}
