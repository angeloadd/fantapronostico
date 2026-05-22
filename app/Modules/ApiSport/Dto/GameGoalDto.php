<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Dto;

final readonly class GameGoalDto
{
    public function __construct(public int $playerApiId, public bool $isOwnGoal, public int $scoredAt) {}
}
