<x-layouts.with-drawer>
    @if($isWinnerDeclared)
        <x-partials.fireworks.fireworks/>
    @endif
    <div class="max-w-6xl mx-auto w-full md:h-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 lg:grid-rows-[1fr_1fr_1fr] gap-6">

        {{-- Row 1: next match, full width --}}
        <div class="md:col-span-3">
            @if($isWinnerDeclared)
                <x-home::shared.winner :$leagueName :$winnerName/>
            @else
                <x-home::shared.next-game :game="$nextGame" :hasFinalStarted="$hasFinalStarted"/>
            @endif
        </div>

        {{-- Row 2: champion (1 col) + ranking (2 cols), grows to fill --}}
        <div class="min-h-0">
            <x-home::shared.champion :champion="$champion" :hasTournamentStarted="$hasTournamentStarted" :tournamentStartedAt="$tournamentStartedAt"/>
        </div>
        <div class="md:col-span-2 md:row-span-2 min-h-0">
            <x-home::shared.ranking :ranking="$ranking"/>
        </div>
        <div class="min-h-0">
            <x-home::shared.stats :userRank="$userRank"/>
        </div>

        {{-- Row 3: last results, full width --}}
        <div class="md:col-span-3">
            <x-home::shared.last-results :$lastResults />
        </div>

    </div>
</x-layouts.with-drawer>
