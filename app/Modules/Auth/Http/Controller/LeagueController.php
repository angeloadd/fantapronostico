<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controller;

use App\Modules\League\Models\League;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class LeagueController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $leagues = League::all();
        /** @var ?Collection<array-key, League> $userLeagues */
        $userLeagues = $request->user()?->leagues;

        $leagues = $leagues->filter(static fn (League $league) => !$userLeagues?->some(static fn (League $l) => $l->id === $league->id));

        if (0 === $leagues->count()) {
            return redirect(route('leagues.pending'));
        }

        return view('auth::index', ['pageName' => 'league', 'leagues' => $leagues]);
    }

    public function requestSubscription(Request $request): RedirectResponse
    {
        $league = League::find($request->input('league_id'));
        if ($league instanceof League) {
            $league->users()->attach($request->user(), ['status' => 'pending']);

            return redirect(route('leagues.pending'));
        }

        abort(404);
    }

    public function subscriptionPending(): View
    {
        return view('auth::index', ['pageName' => 'subscription-pending']);
    }
}
