<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Dto;

final readonly class PlayerDto
{
    public function __construct(
        public int $apiId,
        public string $name,
    ) {}
}
