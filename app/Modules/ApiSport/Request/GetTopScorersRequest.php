<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Request;

final class GetTopScorersRequest
{
    use HasQuery;

    public const ENDPOINT = 'players/topscorers';

    public function __construct(public readonly int $league, public readonly int $season) {}
}
