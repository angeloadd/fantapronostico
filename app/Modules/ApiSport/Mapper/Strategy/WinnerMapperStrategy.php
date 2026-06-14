<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Mapper\Strategy;

use App\Modules\ApiSport\Dto\ApiSportDto;
use App\Modules\ApiSport\Dto\WinnerDto;

final class WinnerMapperStrategy implements MapperStrategyInterface
{
    public function supports(array $externalResponse): bool
    {
        return ($externalResponse['get'] ?? null) === 'fixtures'
            && ($externalResponse['parameters']['round'] ?? null) === 'Final';
    }

    public function map(array $externalResponse): ApiSportDto
    {
        $games = $externalResponse['response'] ?? [];
        if (!is_array($games) || [] === $games) {
            return new WinnerDto(null);
        }

        foreach ($games as $game) {
            if ('Final' !== ($game['league']['round'] ?? null)) {
                continue;
            }

            $winnerTeamId = $game['teams']['home']['winner']
                ? $game['teams']['home']['id']
                : $game['teams']['away']['id'];

            if (!is_numeric($winnerTeamId ?? null)) {
                break;
            }

            return new WinnerDto((int) $winnerTeamId);
        }

        return new WinnerDto(null);
    }
}
