<div class="flex flex-col gap-2">
    <h2 class="text-xs font-medium text-base-content/50 uppercase tracking-wider">Prossimo Incontro</h2>
    <x-home::shared.card>
        <div @class([
            'flex w-full items-center',
            'justify-center' => !isset($game) || $hasFinalStarted,
            'justify-between' => isset($game) && !$hasFinalStarted,
        ])>
            @if(!empty($game ?? null) && !$hasFinalStarted)
                <x-home::shared.team-display :name="__($game->home_team->name)" src="{{$game->home_team->logo}}" alt="{{$game->home_team->name}} Flag"/>
                <div class="flex flex-col items-center gap-3">
                    <x-home::shared.game-date :date="$game->started_at"/>
                    <a href="{{ route('prediction.create', ['game' => $game]) }}" class="btn bg-accent btn-lg rounded-2xl">
                        Pronostica
                    </a>
                </div>
                <x-home::shared.team-display :name="__($game->away_team->name)" src="{{$game->away_team->logo}}" alt="{{$game->away_team->name}} Flag"/>
            @else
                <x-home::shared.illustration img="waiting.svg" alt="A cartoon figure waiting and laying on a tree">
                    @if($hasFinalStarted)
                        Attendi il risultato Finale!<br/>È in corso la finale!
                    @else
                        Il prossimo Incontro non è Disponibile
                    @endif
                </x-home::shared.illustration>
            @endif
        </div>
    </x-home::shared.card>
</div>
