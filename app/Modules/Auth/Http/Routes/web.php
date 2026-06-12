<?php

declare(strict_types=1);

use App\Modules\Auth\Http\Controller\LeagueController;
use App\Modules\Auth\Http\RedirectToLeagueMiddleware;
use Illuminate\Routing\Router;

/** @var Router $r */
$r->middleware('auth')->group(static function (Router $api): void {
    $api->get('/leghe', [LeagueController::class, 'show'])->name('leagues.show');
    $api->post('/leghe/richiesta', [LeagueController::class, 'requestSubscription'])->name('leagues.subscribe');
    $api->middleware([RedirectToLeagueMiddleware::class])->get('leghe/attendi', [LeagueController::class, 'subscriptionPending'])->name('leagues.pending');
    $api->middleware([RedirectToLeagueMiddleware::class])->get('leghe/sospeso', [LeagueController::class, 'banned'])->name('leagues.banned');
    $api->get('/leghe/controlla', [LeagueController::class, 'checkSubscription'])->name('leagues.check');
});
