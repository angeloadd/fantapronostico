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

        foreach ($externalResponse['response'] as $player) {
            if ($player['statistics'][0]['goals']['total'] < $maxGoals) {
                break;
            }

            $maxGoals = max($player['statistics'][0]['goals']['total'], $maxGoals);

            $players[] = new PlayerDto(
                $player['player']['id'],
                $player['player']['name'],
            );

        }

        return new PlayersDto(...$players);
    }
}
