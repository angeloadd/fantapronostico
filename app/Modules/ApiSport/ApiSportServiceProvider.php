<?php

declare(strict_types=1);

namespace App\Modules\ApiSport;

use App\Modules\ApiSport\Client\ApiSportClient;
use App\Modules\ApiSport\Client\ApiSportClientInterface;
use App\Modules\ApiSport\Console\GetGameGoalsCommand;
use App\Modules\ApiSport\Console\GetGamesCommand;
use App\Modules\ApiSport\Console\GetPlayersByTeamCommand;
use App\Modules\ApiSport\Console\GetTeamsCommand;
use App\Modules\ApiSport\Console\SetGameOngoingCommand;
use App\Modules\ApiSport\Mapper\ApiSportMapper;
use App\Modules\ApiSport\Mapper\MapperInterface;
use App\Modules\ApiSport\Mapper\MapperLoggerDecorator;
use App\Modules\ApiSport\Mapper\Strategy\GameGoalsMapperStrategy;
use App\Modules\ApiSport\Mapper\Strategy\GamesMapperStrategy;
use App\Modules\ApiSport\Mapper\Strategy\GameStatusMapperStrategy;
use App\Modules\ApiSport\Mapper\Strategy\NationalsMapperStrategy;
use App\Modules\ApiSport\Mapper\Strategy\TeamsMapperStrategy;
use App\Modules\ApiSport\Mapper\Strategy\TopScorersMapperStrategy;
use App\Modules\ApiSport\Repository\ApiSportGameRepositoryInterface;
use App\Modules\ApiSport\Repository\ApiSportPlayerRepositoryInterface;
use App\Modules\ApiSport\Repository\ApiSportTeamRepositoryInterface;
use App\Modules\ApiSport\Service\ApiSportService;
use App\Modules\ApiSport\Service\ApiSportServiceInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

final class ApiSportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/api-sport.php',
            'api-sport'
        );

        $this->app->bind(
            ApiSportClientInterface::class,
            static fn () => new ApiSportClient((string) config('api-sport.host'), (string) config('api-sport.token'))
        );

        $this->app->bind(ApiSportServiceInterface::class, ApiSportService::class);
        $this->app->bind(MapperInterface::class, static fn (Application $app) => new ApiSportMapper(
            new GamesMapperStrategy(),
            new TeamsMapperStrategy(),
            new NationalsMapperStrategy(),
            new GameStatusMapperStrategy(),
            new GameGoalsMapperStrategy(),
            new TopScorersMapperStrategy()
        ));
        $this->app->extend(
            MapperInterface::class,
            static fn (MapperInterface $mapper) => new MapperLoggerDecorator($mapper, Log::channel('schedule'))
        );

        $this->provideScheduleLogger();
    }

    public function boot(): void {}

    private function provideScheduleLogger(): void
    {
        $this->app->bindMethod(
            [GetTeamsCommand::class, 'handle'],
            static fn (GetTeamsCommand $command, Application $app) => $command->handle(
                $app->make(ApiSportServiceInterface::class),
                Log::channel('schedule'),
                $app->make(ApiSportTeamRepositoryInterface::class)
            )
        );
        $this->app->bindMethod(
            [GetGamesCommand::class, 'handle'],
            static fn (GetGamesCommand $command, Application $app) => $command->handle(
                $app->make(ApiSportServiceInterface::class),
                Log::channel('schedule'),
                $app->make(ApiSportGameRepositoryInterface::class)
            )
        );
        $this->app->bindMethod(
            [GetPlayersByTeamCommand::class, 'handle'],
            static fn (GetPlayersByTeamCommand $command, Application $app) => $command->handle(
                $app->make(ApiSportServiceInterface::class),
                Log::channel('schedule'),
                $app->make(ApiSportPlayerRepositoryInterface::class)
            )
        );
        $this->app->bindMethod(
            [GetGameGoalsCommand::class, 'handle'],
            static fn (GetGameGoalsCommand $command, Application $app) => $command->handle(
                $app->make(ApiSportServiceInterface::class),
                Log::channel('schedule'),
            )
        );
        $this->app->bindMethod(
            [SetGameOngoingCommand::class, 'handle'],
            static fn (SetGameOngoingCommand $command, Application $app) => $command->handle(
                Log::channel('schedule')
            )
        );

        $this->app->when(MapperLoggerDecorator::class)
            ->needs(LoggerInterface::class)
            ->give(static fn (Application $app) => Log::channel('schedule'));

    }
}
