<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http;

use App\Modules\League\Models\League;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class RedirectToLeagueMiddleware
{
    public function handle(Request $request, Closure $next): RedirectResponse|Response
    {
        $league = $request->user()?->selectedLeague;
        if (!$league instanceof League) {

            return $next($request);
        }

        if ('accepted' !== $league->pivot->status) {
            return $next($request);
        }

        return redirect('/');
    }
}
