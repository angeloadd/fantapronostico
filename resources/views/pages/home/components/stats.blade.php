<x-home::shared.card title="Statistiche">
    <div class="grid grid-cols-2 grid-rows-2 gap-6 my-auto">
        <x-home::shared.info-tile label="PUNTI" :value="$userRank->total ?? 0">
            <img src="{{ Vite::asset('resources/assets/images/winner.svg') }}" class="size-5" alt="trophy">
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="ESATTI" :value="$userRank->numberOfResults ?? 0">
            <img src="{{ Vite::asset('resources/assets/images/golden_boot.svg') }}" class="size-5" alt="golden boot">
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="SEGNI" :value="$userRank->numberOfSigns ?? 0">
            <img src="{{ Vite::asset('resources/assets/images/winner.svg') }}" class="size-5" alt="trophy">
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="GOL" :value="$userRank->numberOfScorers ?? 0">
            <img src="{{ Vite::asset('resources/assets/images/golden_boot.svg') }}" class="size-5" alt="golden boot">
        </x-home::shared.info-tile>
    </div>
</x-home::shared.card>