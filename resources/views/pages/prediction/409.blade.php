<x-prediction::shared.layout>
    <x-prediction::shared.game-bar :games="$games" :game="$game"/>
    <div class="flex justify-center items-center px-2 text-center text-neutral">
        <div role="alert" class="alert border-accent flex flex-col justify-center items-center shadow-lg text-lg">
            <h3 class="text-neutral">
                Partita pronosticabile dal <strong>
                    {{str($game->predictable_from->isoFormat('D MMMM YYYY \a\l\l\e HH:mm'))->title()->replace('Alle','alle')}}
                </strong>
            </h3>
            <div class="flex w-full items-center justify-between text-neutral">
                <x-home::shared.team-display :name="__($game->home_team->name)" src="{{$game->home_team->logo}}" alt="{{$game->home_team->name}} Flag"/>
                <x-home::shared.game-date :date="$game->started_at"/>
                <x-home::shared.team-display :name="__($game->away_team->name)" src="{{$game->away_team->logo}}" alt="{{$game->away_team->name}} Flag"/>
            </div>
            <x-partials.countdown.main bgColor="bg-yellow-500" :date="$game->started_at"/>
        </div>
    </div>
</x-prediction::shared.layout>
