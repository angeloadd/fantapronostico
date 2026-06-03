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
        foreach ($externalResponse['response'] as $item) {
            if ('Final' !== ($item['league']['round'] ?? null)) {
                continue;
            }

            $winnerTeamId = $item['teams']['home']['winner']
                ? $item['teams']['home']['id']
                : $item['teams']['away']['id'];

            return new WinnerDto($winnerTeamId);
        }

        return new WinnerDto(null);
    }
}