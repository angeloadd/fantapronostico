<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Request;

final readonly class GetGamesRequest
{
    use HasQuery;

    public const ENDPOINT = 'fixtures';

    public function __construct(
        public int $league,
        public int $season
    ) {}
}
