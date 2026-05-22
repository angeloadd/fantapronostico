<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Dto;

use App\Enums\GameStatus;

final class GameStatusDto implements ApiSportDto
{
    public function __construct(public readonly int $apiId, public readonly GameStatus $status) {}
}
