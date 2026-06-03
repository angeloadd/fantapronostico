<div class="flex flex-col gap-2">
    <x-shared.card-title>{{ __('messages.home.last_results_title') }}</x-shared.card-title>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($lastResults->take(3) as $result)
            <a href="{{ route('prediction.index', ['game' => $result]) }}"
               class="card bg-base-100 border border-base-300 shadow-sm rounded-xl hover:border-accent/60 hover:shadow-md transition-all">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex flex-col items-center gap-1 flex-1">
                            <span class="text-4xl">{{\App\Helpers\FunWithFlags::getFlag($result->home_team->code)}}</span>
                            <span class="text-xs text-center font-medium leading-tight">{{ __($result->home_team->name) }}</span>
                        </div>
                        <div class="text-center shrink-0">
                            <span class="font-bold text-xl tabular-nums">{{ $result->home_score }} - {{ $result->away_score }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 flex-1">
                            <span class="text-4xl">{{\App\Helpers\FunWithFlags::getFlag($result->away_team->code)}}</span>
                            <span class="text-xs text-center font-medium leading-tight">{{ __($result->away_team->name) }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
