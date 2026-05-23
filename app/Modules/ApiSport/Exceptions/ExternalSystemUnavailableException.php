<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Exceptions;

use RuntimeException;

final class ExternalSystemUnavailableException extends RuntimeException
{
    public static function fromResponse(string $response): self
    {
        return new self('External system unavailable: '.$response, 424);
    }
}
