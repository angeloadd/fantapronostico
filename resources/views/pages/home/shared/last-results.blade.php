<div class="flex flex-col gap-2">
    <h2 class="text-xs font-medium text-base-content/50 uppercase tracking-wider">Ultimi Risultati</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($lastResults->take(3) as $result)
            <a href="{{ route('prediction.index', ['game' => $result]) }}"
               class="card bg-base-100 border border-base-300 shadow-sm rounded-xl hover:border-accent/60 hover:shadow-md transition-all">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex flex-col items-center gap-1 flex-1">
                            <img class="size-8 object-contain" src="{{ $result->home_team->logo }}" alt="{{ $result->home_team->name }}">
                            <span class="text-xs text-center font-medium leading-tight">{{ __($result->home_team->name) }}</span>
                        </div>
                        <div class="text-center shrink-0">
                            <span class="font-bold text-xl tabular-nums">{{ $result->home_score }} - {{ $result->away_score }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 flex-1">
                            <img class="size-8 object-contain" src="{{ $result->away_team->logo }}" alt="{{ $result->away_team->name }}">
                            <span class="text-xs text-center font-medium leading-tight">{{ __($result->away_team->name) }}</span>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <div class="badge bg-secondary rounded-full text-xs font-semibold">
                            +7 punti
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
