<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\CalculateRanking;
use App\Events\GameGoalsUpdated;
use App\Helpers\Ranking\RankingCalculatorInterface;
use App\Helpers\Ranking\ViewRankingCalculator;
use App\Modules\ApiSport\Repository\ApiSportGameRepositoryInterface;
use App\Modules\ApiSport\Repository\ApiSportPlayerRepositoryInterface;
use App\Modules\ApiSport\Repository\ApiSportTeamRepositoryInterface;
use App\Modules\League\Models\League;
use App\Modules\League\Service\Telegram\TelegramService;
use App\Modules\League\Service\Telegram\TelegramServiceInterface;
use App\Modules\Tournament\Repository\PlayerRepository;
use App\Modules\Tournament\Repository\TeamRepository;
use App\Repository\Game\GameRepository;
use App\Repository\Game\GameRepositoryInterface;
use App\Repository\Prediction\PredictionRepository;
use App\Repository\Prediction\PredictionRepositoryInterface;
use App\Service\RequestProvider\RequestProviderService;
use App\Service\RequestProvider\RequestProviderServiceInterface;
use Event;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PredictionRepositoryInterface::class, PredictionRepository::class);
        $this->app->bind(GameRepositoryInterface::class, GameRepository::class);
        $this->app->bind(ApiSportGameRepositoryInterface::class, GameRepository::class);
        $this->app->bind(
            RankingCalculatorInterface::class,
            static fn (Application $app) => new ViewRankingCalculator(
                Log::channel('worker')
            )
        );
        $this->app->bind(ApiSportTeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(ApiSportPlayerRepositoryInterface::class, PlayerRepository::class);

        $this->app->bind(TelegramServiceInterface::class, static fn (Application $app) => new TelegramService(
            Log::channel('worker')
        ));
        $this->app->bind(RequestProviderServiceInterface::class, RequestProviderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPagesNamespace();

        $this->registerRequestMacro();

        Event::listen(GameGoalsUpdated::class, GameGoalsUpdated::class);
    }

    private function registerPagesNamespace(): void
    {
        $foldersInPages = glob(resource_path('/views/pages').'/**/');
        if (is_array($foldersInPages)) {
            foreach ($foldersInPages as $folder) {
                Blade::anonymousComponentPath(
                    $folder,
                    str($folder)
                        ->explode('/')
                        ->last(static fn ($folder) => '' !== $folder)
                );
            }
        }
    }

    private function registerRequestMacro(): void
    {
        if (!Request::hasMacro('getCurrentLeague')) {
            Request::macro('getCurrentLeague', function (): League {
                $league = request()->league;
                if (!$league instanceof League) {
                    throw new InvalidArgumentException('Current league cannot be retrieved');
                }

                return $league;
            });
        }
    }
}
