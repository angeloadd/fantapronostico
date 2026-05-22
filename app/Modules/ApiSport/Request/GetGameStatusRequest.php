<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Request;

final class GetGameStatusRequest
{
    use HasQuery;

    public const ENDPOINT = 'fixtures';

    public function __construct(public readonly int $id) {}
}
