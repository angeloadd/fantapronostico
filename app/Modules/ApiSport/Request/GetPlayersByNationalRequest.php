<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Request;

final readonly class GetPlayersByNationalRequest
{
    use HasQuery;

    public const ENDPOINT = '/players/squads';

    public function __construct(public int $team) {}
}
