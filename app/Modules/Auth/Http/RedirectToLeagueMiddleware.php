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
        if ($request->user()->selectedLeague instanceof League) {
            return redirect('/')->with('success', 'La tua richiesta è stata approvata.');
        }

        return $next($request);
    }
}
