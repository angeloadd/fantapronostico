<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Dto;

final readonly class WinnerDto implements ApiSportDto
{
    public function __construct(public readonly ?int $teamApiId) {}
}