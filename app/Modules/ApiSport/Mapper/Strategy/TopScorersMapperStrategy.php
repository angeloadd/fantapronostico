<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Mapper\Strategy;

use App\Modules\ApiSport\Dto\ApiSportDto;
use App\Modules\ApiSport\Dto\PlayerDto;
use App\Modules\ApiSport\Dto\PlayersDto;

final class TopScorersMapperStrategy implements MapperStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(array $externalResponse): bool
    {
        return 'players/topscorers' === $externalResponse['get'];
    }

    /**
     * {@inheritDoc}
     */
    public function map(array $externalResponse): ApiSportDto
    {
        $players = [];
        $maxGoals = 0;

        $playersFromResponse = $externalResponse['response'] ?? [];

        if (!is_array($playersFromResponse) || [] === $playersFromResponse) {
            return new PlayersDto();
        }

        foreach ($playersFromResponse as $player) {
            $playerTotalGoals = $player['statistics'][0]['goals']['total'] ?? null;
            if (!is_numeric($playerTotalGoals)) {
                continue;
            }
            if ($playerTotalGoals < $maxGoals) {
                break;
            }

            $maxGoals = max($playerTotalGoals, $maxGoals);

            $players[] = new PlayerDto(
                (int) ($player['player']['id'] ?? null),
                (string) ($player['player']['name'] ?? null),
            );

        }

        return new PlayersDto(...$players);
    }
}
