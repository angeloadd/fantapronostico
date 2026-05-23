<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Exceptions;

use RuntimeException;
use Throwable;

final class ApiSportParsingException extends RuntimeException
{
    public static function withCause(Throwable $exception): self
    {
        return new self(
            'Error parsing API Sport response: '.$exception->getMessage(),
            400,
            $exception
        );
    }
}
