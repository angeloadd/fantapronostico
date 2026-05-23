<?php

declare(strict_types=1);

use App\Http\Controllers\ChampionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\RankingController;
use App\Http\Middleware\LeagueEnricherMiddleware;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group([], static function (Router $r): void {
    $routesFromModules = glob(__DIR__.'/../app/Modules/*/Http/Routes/*.php');
    if (!$routesFromModules) {
        $routesFromModules = [];
    }
    foreach ($routesFromModules as $moduleRoutesPath) {
        require $moduleRoutesPath;
    }
});

Route::middleware(['auth', LeagueEnricherMiddleware::class])->group(static function (): void {
    /* homepage */
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware([EnsureEmailIsVerified::class])->group(static function (): void {
        /* Bet CRUD */
        Route::get('/incontro/prossimo/{game?}', [PredictionController::class, 'nextGame'])->name('prediction.next-from-ref');
        Route::get('/incontro/precedente/{game}', [PredictionController::class, 'previousGameFromReference'])->name('prediction.previous-from-ref');
        Route::get('/pronostici/incontro/{game}', [PredictionController::class, 'index'])->name('prediction.index');
        Route::get('/pronostico/incontro/{game}', [PredictionController::class, 'show'])->name('prediction.show');
        Route::get('/pronostico/incontro/{game}/crea', [PredictionController::class, 'create'])->name('prediction.create');
        Route::post('/pronostico/incontro/{game}/store', [PredictionController::class, 'store'])->name('prediction.store');
        Route::get('/pronostico/modifica/{prediction}', [PredictionController::class, 'edit'])->name('prediction.edit');
        Route::put('/pronostico/aggiorna/{prediction}', [PredictionController::class, 'update'])->name('prediction.update');
        Route::get('/pronostico/bloccato/{game}', [PredictionController::class, 'gameNotPredictable'])->name('prediction.409');

        /* CRUD per vincitore e capocannoniere */
        Route::get('/pronostico/vincitore/index', [ChampionController::class, 'index'])->name('champion.index');
        Route::get('/pronostico/vincitore/create', [ChampionController::class, 'create'])->name('champion.create');
        Route::post('/pronostico/vincitore/store', [ChampionController::class, 'store'])->name('champion.store');
        Route::get('/pronostico/vincitore/edit/{champion}', [ChampionController::class, 'edit'])->name('champion.edit');
        Route::post('/pronostico/vincitore/update/{champion}', [ChampionController::class, 'update'])->name('champion.update');
        Route::get('/pronostico/vincitore/show/{champion}', [ChampionController::class, 'show'])->name('champion.show');
        Route::get('/pronostico/vincitore/error', [ChampionController::class, 'error'])->name('champion.409');
    });

    // classifica
    Route::get('classifica', [RankingController::class, 'officialStanding'])->name('standing');

    Route::get('albo', [MiscController::class, 'albo'])->name('albo');
    Route::get('terms', [MiscController::class, 'terms'])->name('terms');
});
