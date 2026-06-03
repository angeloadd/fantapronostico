<x-home::shared.card title="{{ __('messages.home.stats_title') }}">
    <div class="grid grid-cols-2 grid-rows-2 gap-6 my-auto">
        <x-home::shared.info-tile label="{{ __('messages.stats.points') }}" :value="$userRank->total ?? 0">
            <x-partials.svgs.rank/>
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="{{ __('messages.stats.exact') }}" :value="$userRank->numberOfResults ?? 0">
            <x-partials.svgs.bet/>
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="{{ __('messages.stats.signs') }}" :value="$userRank->numberOfSigns ?? 0">
            <x-partials.svgs.signs/>
        </x-home::shared.info-tile>
        <x-home::shared.info-tile label="{{ __('messages.stats.goals') }}" :value="$userRank->numberOfScorers ?? 0">
            <x-partials.svgs.boot/>
        </x-home::shared.info-tile>
    </div>
</x-home::shared.card>
