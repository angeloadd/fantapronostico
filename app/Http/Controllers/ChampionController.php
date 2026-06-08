<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChampionRequest;
use App\Models\Champion;
use App\Models\Player;
use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use App\Modules\Tournament\Models\Team;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class ChampionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function create(): Renderable|RedirectResponse
    {
        if ($this->isChampionBetAvailableByDate()) {
            return redirect(route('champion.409'))->with('La competizione non è ancora iniziata');
        }

        if ($this->competitionStarted()) {
            return redirect(route('champion.index'));
        }

        $champion = Auth::user()->champion;
        if ($champion) {
            return redirect(route('champion.show', compact('champion')));
        }

        $tournament = Tournament::first();

        return view(
            'pages.champion.create',
            [
                'teams' => Team::all(),
                'players' => Player::getAllPlayerCached(),
                'firstMatchDate' => $this->getFirstMatchStartDate(),
                'tournamentLogo' => $tournament?->logo,
                'tournamentName' => $tournament?->name,
            ]
        );
    }

    public function store(ChampionRequest $request): RedirectResponse
    {
        if ($this->competitionStarted()) {
            return redirect(route('champion.index'))
                ->with(
                    'error_message',
                    'La competizione è già iniziata, non puoi più inserire o modificare un pronostico'
                );
        }

        $user = Auth::user();
        if (!$user instanceof User) {
            abort(404);
        }

        $leagueId = $user->selected_league_id;

        $champion = Champion::whereUserId($user->id)
            ->where('league_id', $leagueId)
            ->first();

        if ($champion) {
            return redirect(route('champion.show', compact('champion')));
        }

        $champion = $user->champion()->create([
            'team_id' => $request->input('winner'),
            'player_id' => $request->input('topScorer'),
            'league_id' => $leagueId,
        ]);

        return redirect(route('champion.show', compact('champion')))
            ->with('message', 'Pronostico inserito con successo');
    }

    public function edit(Champion $champion): RedirectResponse|Renderable
    {
        if ($this->competitionStarted()) {
            return redirect(route('champion.index'))->with('error_message', 'La competizione è già iniziata');
        }

        if (Auth::user()?->id !== $champion->user_id) {
            abort(404);
        }

        $tournament = Tournament::first();

        return view(
            'pages.champion.edit',
            [
                'teams' => Team::all(),
                'players' => Player::getAllPlayerCached(),
                'champion' => $champion,
                'firstMatchDate' => $this->getFirstMatchStartDate(),
                'tournamentLogo' => $tournament?->logo,
                'tournamentName' => $tournament?->name,
            ]
        );
    }

    public function update(ChampionRequest $request, Champion $champion): RedirectResponse
    {
        if ($this->competitionStarted()) {
            return redirect(route('champion.index'))
                ->with(
                    'error_message',
                    'La competizione è già iniziata, non puoi più inserire o modificare un pronostico'
                );
        }

        if (Auth::user()?->id !== $champion->user_id) {
            abort(404);
        }

        $champion->update([
            'team_id' => $request->input('winner'),
            'player_id' => $request->input('topScorer'),
        ]);

        return redirect(route('champion.show', compact('champion')))
            ->with('message', 'pronostico aggiornato con successo');
    }

    public function show(Champion $champion): Renderable|RedirectResponse
    {
        if ($this->competitionStarted()) {
            return redirect(route('champion.index'))->with('error_message', 'La competizione è iniziata');
        }

        if (Auth::user()?->id !== $champion->user_id) {
            abort(404);
        }

        $tournament = Tournament::first();

        return view(
            'pages.champion.show',
            compact('champion'),
            [
                'firstMatchDate' => $this->getFirstMatchStartDate(),
                'tournamentLogo' => $tournament?->logo,
                'tournamentName' => $tournament?->name,
            ]
        );
    }

    public function index(): RedirectResponse|Renderable
    {
        if (!$this->competitionStarted()) {
            return redirect(route('champion.create'));
        }

        return view(
            'pages.champion.index',
            ['champions' => Champion::all()]
        );
    }

    public function error(): Renderable|RedirectResponse
    {
        $tournament = Tournament::first();
        $firstMatchStartDate = Tournament::first()?->first()?->started_at;
        $championSettableFrom = $firstMatchStartDate->avoidMutation()->subDays(2);

        if ($championSettableFrom->isPast()) {
            return redirect(route('champion.index'));
        }

        return view('pages.champion.409', [
            'championSettableFrom' => $championSettableFrom,
            'tournamentLogo' => $tournament?->logo,
            'tournamentName' => $tournament?->name,
            'firstMatchDate' => $firstMatchStartDate,
        ]);
    }

    public function getFirstMatchStartDate(): ?Carbon
    {
        return Tournament::first()?->first()?->started_at;
    }

    private function getChampionSettableFrom(): ?Carbon
    {
        return $this->getFirstMatchStartDate()?->subDays(2);
    }

    private function isChampionBetAvailableByDate(): bool
    {
        return time() <= ($this->getChampionSettableFrom()?->unix() ?? 0);
    }

    private function competitionStarted(): bool
    {
        return (bool) $this->getFirstMatchStartDate()?->isPast();
    }
}
