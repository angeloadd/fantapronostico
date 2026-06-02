<x-home::shared.card title="Statistiche">
    <div class="grid grid-cols-2 grid-rows-2 gap-6 my-auto">
        <x-home::shared.info-tile label="PUNTI" :value="$userRank->total ?? 0">
            <x-partials.svgs.rank/>
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="ESATTI" :value="$userRank->numberOfResults ?? 0">
            <x-partials.svgs.bet/>
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="SEGNI" :value="$userRank->numberOfSigns ?? 0">
            <x-partials.svgs.signs/>
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="GOL" :value="$userRank->numberOfScorers ?? 0">
            <x-partials.svgs.boot/>
        </x-home::shared.info-tile>
    </div>
</x-home::shared.card>
