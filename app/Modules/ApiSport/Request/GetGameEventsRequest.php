<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Request;

final class GetGameEventsRequest
{
    use HasQuery;

    public const ENDPOINT = 'fixtures/events';

    public function __construct(public readonly int $fixture, public readonly string $type = 'Goal') {}
}
