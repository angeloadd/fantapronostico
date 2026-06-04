<?php

declare(strict_types=1);

use App\Helpers\FormatDate;
use App\Helpers\FunWithFlags;
use Illuminate\Support\Carbon;

if (!function_exists('flagEmoji')) {
    function flagEmoji(string $countryCode): string
    {
        return FunWithFlags::getFlag($countryCode);
    }
}
