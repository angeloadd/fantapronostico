<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Request;

final class GetWinnerRequest
{
    use HasQuery;

    public const ENDPOINT = 'fixtures';

    public function __construct(
        public readonly int $league,
        public readonly int $season,
        public readonly string $round = 'Final',
    ) {}
}
