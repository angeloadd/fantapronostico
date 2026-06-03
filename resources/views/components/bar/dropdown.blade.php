@php
/** @var \Illuminate\Support\Collection $games */
$gameGrouped = $games->groupBy(static fn(\App\Models\Game $game) => $game->status === \App\Enums\GameStatus::NOT_STARTED ? 'not_started' : 'started');
$notStarted = $gameGrouped->get('not_started') ?? collect();
$started = $gameGrouped->get('started') ?? collect();
@endphp

<div class="shadow-lg dropdown-content bg-base-100 overflow-y-auto w-72 max-h-80 rounded-box flex flex-col">
    @if($notStarted->isNotEmpty())
        <div class="flex items-center pl-6 py-3 border-b border-base-300 text-base-content/60 font-bold text-xs tracking-wider bg-base-300/50 shrink-0">
            {{ __('messages.bar.games_upcoming') }}
        </div>
        <x-bar.games :games="$notStarted" :$game/>
    @endif
    @if($started->isNotEmpty())
        <div class="flex items-center pl-6 py-3 border-b border-base-300 text-base-content/60 font-bold text-xs tracking-wider bg-base-300/50 shrink-0">
            {{ __('messages.bar.games_played') }}
        </div>
        <x-bar.games :games="$started" :$game :disabled="true"/>
    @endif
</div>
