<?php

declare(strict_types=1);
use App\Modules\ApiSport\ApiSportServiceProvider;
use App\Modules\Auth\AuthServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    ApiSportServiceProvider::class,
    AuthServiceProvider::class,
    AppServiceProvider::class,
    AdminPanelProvider::class,
];
