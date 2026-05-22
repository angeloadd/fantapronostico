<?php

declare(strict_types=1);

namespace App\Modules\Auth\Enums;

use App\Enums\HasValues;

enum RoleEnum: string
{
    use HasValues;

    case ADMIN = 'admin';
    case MOD = 'mod';
}
