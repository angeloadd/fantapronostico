<?php

declare(strict_types=1);

use App\Helpers\FunWithFlags;

if (!function_exists('flagEmoji')) {
    function flagEmoji(string $countryCode): string
    {
        return FunWithFlags::getFlag($countryCode);
    }
}
