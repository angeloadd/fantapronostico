<x-layouts.with-drawer>
    @if($isWinnerDeclared)
        <x-partials.fireworks.fireworks/>
    @endif
    <div class="max-w-6xl mx-auto w-full md:h-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 lg:grid-rows-[1fr_1fr_1fr] gap-6">

        {{-- Row 1: next match, full width --}}
        @if($isWinnerDeclared)
            <div class="min-h-0 md:col-span-3 flex justify-center items-center order-1">
                <x-home::components.winner :$leagueName :$winnerName/>
            </div>
        @else
            <div
                    @class([
                        'min-h-0 md:col-span-1 flex justify-center items-center order-2 md:order-1',
                        'hidden' => ($openGames ?? collect())->isEmpty()
            ])>
                <x-home::components.modifiable-games :openGames="$openGames"/>
            </div>
            <div class="min-h-0 md:col-span-2 flex justify-center items-center order-1 md:order-2">
                <x-home::components.next-game :game="$nextGame ?? null" :hasFinalStarted="$hasFinalStarted"/>
            </div>
{{--            @if(null !== ($predictedGames ?? null) && $predictedGames->isNotEmpty())--}}
{{--                <div class="min-h-0 md:col-span-1 flex justify-center items-center order-1">--}}
{{--                    <x-home::components.modifiable-games :predictedGames="$predictedGames"/>--}}
{{--                </div>--}}
{{--                <div class="min-h-0 md:col-span-2 flex justify-center items-center order-1">--}}
{{--                    <x-home::components.next-game :game="$nextGame ?? null" :hasFinalStarted="$hasFinalStarted" :$nextGameCta/>--}}
{{--                </div>--}}
{{--            @else--}}
{{--                <div class="min-h-0 md:col-span-3 flex justify-center items-center order-1">--}}
{{--                    <x-home::components.next-game :game="$nextGame ?? null" :hasFinalStarted="$hasFinalStarted" :$nextGameCta />--}}
{{--                </div>--}}
{{--            @endif--}}
        @endif

        {{-- Row 2: champion (1 col) + ranking (2 cols), grows to fill --}}
        <div class="min-h-0 md:col-span-2 md:row-span-2 order-2 md:order-3">
            <x-home::components.ranking :ranking="$ranking"/>
        </div>
        <div class="min-h-0 order-3 md:order-2">
            <x-home::components.champion
                    :champion="$champion ?? null"
                    :tournamentStartedAt="$tournamentStartedAt"
                    :isChampionPredictionSuccessful="$userRank->winner ?? false"
                    :isTopScorerPredictionSuccessful="$userRank->topScorer ?? false"
            />
        </div>
        <div class="min-h-0 order-4">
            <x-home::components.stats :userRank="$userRank"/>
        </div>

        {{-- Row 3: last results, full width --}}
        <div class="md:col-span-3 order-5">
            <x-home::components.last-results :$lastResults/>
        </div>

    </div>
</x-layouts.with-drawer>
