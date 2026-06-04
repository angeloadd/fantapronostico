@foreach($games as $gameInBar)
    @if(isset($gameInBar->home_team, $gameInBar->away_team))
        <a
            @class([
                'flex items-center justify-center py-3 w-full text-sm border-b border-base-300',
                'px-3' => !($disabled ?? false),
                'px-1 text-base-content/50' => $disabled ?? false,
                'bg-accent/30' => $game?->id === $gameInBar->id,
                'hover:bg-base-300/60' => $game?->id !== $gameInBar->id,
            ])
            href="{{ route('prediction.index', ['game' => $gameInBar]) }}"
        >
            {{ __($gameInBar->home_team->name) }} {{ flagEmoji($gameInBar->home_team->code) }} vs {{ flagEmoji($gameInBar->away_team->code) }} {{ __($gameInBar->away_team->name) }}
        </a>
    @endif
@endforeach
