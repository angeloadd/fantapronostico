<x-home::shared.card title="Modifica Pronostici" :grayed="true">
    <div class="flex flex-col items-center justify-around gap-1 h-full">
        @foreach($openGames as $openGame)
            <div class="flex items-center justify-between gap-1 bg-base-200 {{$openGames->count() > 3 ? 'p-1 md:p-0.5' : 'p-1'}} rounded-xl pr-3 w-full">
                <div class="flex items-center justify-center gap-1 flex-1">
                    <span class="text-xs text-center font-bold leading-tight text-base-content/80">{{ __($openGame->home_team->code) }}</span>
                    <span class="{{$openGames->count() > 3 ? 'text-xl' : 'text-4xl'}}">{{flagEmoji($openGame->home_team->code)}}</span>
                </div>
                <div class="text-center shrink-0">
                    <span class="font-bold text-sm text-base-content/50">VS</span>
                </div>
                <div class="flex justify-center items-center gap-1 flex-1">
                    <span class="{{$openGames->count() > 3 ? 'text-xl' : 'text-4xl'}}">{{flagEmoji($openGame->away_team->code)}}</span>
                    <span class="text-xs text-center font-bold leading-tight text-base-content/80">{{ __($openGame->away_team->code) }}</span>
                </div>
                <a
                        class="shrink btn {{$openGames->count() > 3 ? 'md:btn-xs btn-sm' : 'btn-sm'}} btn-{{$openGame->isPredicted ? 'primary' : 'accent btn-outline'}}"
                        href="{{ route('prediction.index', ['game' => $openGame])}}">
                    {{$openGame->isPredicted ? 'Modifica' : 'Pronostica'}}
                </a>
            </div>
        @endforeach
    </div>
</x-home::shared.card>
