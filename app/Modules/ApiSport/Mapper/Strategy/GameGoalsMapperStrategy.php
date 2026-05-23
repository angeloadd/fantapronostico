<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Mapper\Strategy;

use App\Modules\ApiSport\Dto\ApiSportDto;
use App\Modules\ApiSport\Dto\GameGoalDto;
use App\Modules\ApiSport\Dto\GameGoalsDto;

final class GameGoalsMapperStrategy implements MapperStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(array $externalResponse): bool
    {
        if (!array_key_exists('get', $externalResponse)) {
            return false;
        }

        return 'fixtures/events' === $externalResponse['get'];
    }

    /**
     * {@inheritDoc}
     */
    public function map(array $externalResponse): ApiSportDto
    {
        $gameGoals = new GameGoalsDto();

        if (!is_array($externalResponse['response'])) {
            return $gameGoals;
        }

        foreach ($externalResponse['response'] as $event) {
            // If a goal is a penalty in the shootouts skip
            if (120 === ($event['time']['elapsed'] ?? null) && null !== $event['time']['extra'] && str_contains((string) $event['comments'], 'Penalty')) {
                continue;
            }

            // if a player missed a penalty we don't care
            if (str_contains((string) $event['detail'], 'Missed')) {
                continue;
            }

            $gameGoals->add(new GameGoalDto(
                (int) $event['player']['id'],
                str_contains((string) $event['detail'], 'Own'),
                $event['time']['elapsed'] + ($event['time']['extra'] ?? 0)
            ));
        }

        return $gameGoals;
    }
}
