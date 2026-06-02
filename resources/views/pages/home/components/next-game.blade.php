<div class="w-full">
    <x-home::shared.card title="Prossimo Incontro">
        <div @class([
            'my-auto flex w-full items-center',
            'justify-center'  => !isset($game) || $hasFinalStarted,
            'justify-between' => isset($game) && !$hasFinalStarted,
        ])>
            @if(!empty($game ?? null) && !$hasFinalStarted)
                <div class="w-full flex flex-col justify-center items-center">
                    <span class="text-8xl">{{ \App\Helpers\FunWithFlags::getFlag($game->home_team->code) }}</span>
                    <h3 class="sm:text-lg font-bold text-center whitespace-nowrap">{{ $game->home_team->name }}</h3>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <x-home::shared.game-date :date="$game->started_at"/>
                    <a href="{{ route('prediction.create', ['game' => $game]) }}" class="btn bg-accent btn-lg rounded-2xl">
                        Pronostica
                    </a>
                </div>
                <div class="w-full flex flex-col justify-center items-center">
                    <span class="text-8xl">{{ \App\Helpers\FunWithFlags::getFlag($game->away_team->code) }}</span>
                    <h3 class="sm:text-lg font-bold text-center whitespace-nowrap">{{ $game->away_team->name }}</h3>
                </div>
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