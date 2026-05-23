<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class LeagueEnricherMiddleware
{
    public function handle(Request $request, Closure $next): RedirectResponse|Response
    {
        $user = $request->user();

        if (!$user instanceof User || $user->selectedLeague instanceof League) {
            return $next($request);
        }

        $leagues = $user->leagues->filter(static fn (League $league) => $league->pivot->user_id === $user->id && 'pending' !== $league->pivot->status);

        if ($leagues->count() > 0) {
            $user->selected_league_id = $leagues->first()->id;
            $user->save();

            return $next($request);
        }

        return redirect(route('leagues.show'))->with('error_message', 'Devi essere iscritto ad una lega per accedere');
    }
}
