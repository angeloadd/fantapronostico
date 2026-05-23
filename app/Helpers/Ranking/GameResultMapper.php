<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use App\Models\Player;
use App\Models\Prediction;

final class GameResultMapper
{
    public static function map(Prediction $prediction): GameResult
    {
        $game = $prediction->game;

        return new GameResult(
            PredictionScoreFactory::create($prediction),
            $game->id,
            $game->home_score,
            $game->away_score,
            $game->sign,
            self::mapScorers($game->home_scorers),
            self::mapScorers($game->away_scorers),
        );
    }

    /**
     * @param  int[]  $scorers
     * @return string[]
     */
    public static function mapScorers(array $scorers): array
    {
        return array_map(
            static function (?int $scorerId): string {
                if (null === $scorerId) {
                    return 'N/A';
                }
                if (-1 === $scorerId) {
                    return 'Autogol';
                }

                if (0 === $scorerId) {
                    return 'No Gol';
                }

                $player = Player::find($scorerId);
                if (null === $player) {
                    return 'N/A';
                }

                return $player->displayed_name.' ('.__($player->national?->name).')';
            },
            $scorers
        );
    }
}
