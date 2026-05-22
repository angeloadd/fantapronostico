<?php

declare(strict_types=1);

namespace App\Enums;

use InvalidArgumentException;
use ReflectionClass;

trait HasValues
{
    /**
     * @return array<int|string>
     */
    public static function values(): array
    {
        if ( ! (new ReflectionClass(self::class))->isEnum()) {
            throw new InvalidArgumentException('This trait can be only used by enum classes');
        }

        return array_map(
            static fn (self $enumCase) => $enumCase->value ?? mb_strtolower($enumCase->name),
            self::cases()
        );
    }
}
