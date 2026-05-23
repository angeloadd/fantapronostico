<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Mapper\Strategy;

use App\Enums\GameStatus;
use App\Modules\ApiSport\Dto\ApiSportDto;
use App\Modules\ApiSport\Dto\GameStatusDto;

final class GameStatusMapperStrategy implements MapperStrategyInterface
{
    private const FINISHED_STATUSES = [
        'FT',
        'AET',
        'PEN',
    ];

    private const ONGOING_STATUSES = ['1H', 'HT', '2H', 'ET', 'BT', 'P', 'SUSP', 'INT', 'LIVE'];

    /**
     * {@inheritDoc}
     */
    public function supports(array $externalResponse): bool
    {
        if (!array_key_exists('get', $externalResponse)) {
            return false;
        }

        if ('fixtures' !== $externalResponse['get']) {
            return false;
        }

        if (!array_key_exists('parameters', $externalResponse)) {
            return false;
        }

        return !(!array_key_exists('id', $externalResponse['parameters']));
    }

    /**
     * {@inheritDoc}
     */
    public function map(array $externalResponse): ApiSportDto
    {
        $statusFromApi = $externalResponse['response'][0]['fixture']['status']['short'];

        if (in_array($statusFromApi, self::FINISHED_STATUSES, true)) {
            $status = GameStatus::FINISHED;
        } elseif (in_array($statusFromApi, self::ONGOING_STATUSES, true)) {
            $status = GameStatus::ONGOING;
        } else {
            $status = GameStatus::NOT_STARTED;
        }

        return new GameStatusDto(
            (int) $externalResponse['response'][0]['fixture']['id'],
            $status
        );
    }
}
