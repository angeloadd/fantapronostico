<div class="w-full flex flex-col justify-center items-center">
    @if($isGameInTheFuture)
        <div class="ml-auto pb-2 sm:pb-3">
            <x-partials.countdown.main :date="$startedAt" />
        </div>
    @endif
    <div class="w-full flex items-center">
        <x-home::shared.team-display :name="$homeTeamName" :src="$homeTeamLogo" :alt="$homeTeamName.' Flag'"/>
        <x-home::shared.game-date :date="$startedAt"/>
        <x-home::shared.team-display :name="$awayTeamName" :src="$awayTeamLogo" :alt="$awayTeamName.' Flag'"/>
    </div>
</div>
